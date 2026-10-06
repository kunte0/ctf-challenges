import smtplib

MAIL_SERVER = "pushgainpull.club"  # Your mail server
MAIL_SERVER_PORT = 25  # Port 25 for SMTP
SENDER_EMAIL = "admin@pushgainpull.club"  # Sender's email address
RECIPIENT_EMAIL = "checker@pushgainpull.club"  # Recipient email address
# load eml

with open('./spoofed.eml', 'r') as f:
    email_text = f.read()


# send eml

server = smtplib.SMTP(MAIL_SERVER, MAIL_SERVER_PORT)
server.set_debuglevel(1)
server.sendmail(SENDER_EMAIL, RECIPIENT_EMAIL, email_text)
server.quit()

