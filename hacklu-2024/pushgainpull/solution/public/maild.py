import gnupg
import asyncio
from aiosmtpd.controller import Controller
from os import environ


SENDER_EMAIL = 'admin@pushgainpull.club' # Sender's email address
SENDER_USERNAME = 'admin' # Sender's username
RECIPIENT_EMAIL = 'checker@pushgainpull.club' # Recipient email address
MAIL_SERVER = 'pushgainpull.club'
MAIL_SERVER_PORT = 25 # Port 25 for SMTP
INSTRUCTION_BODY = 'Give Flag'
FLAG = environ.get('FLAG', 'flag{fake_flag}')
PASSPHRASE = environ.get('PASSPHRASE', '***********censored***********')

gpg = gnupg.GPG(gnupghome='./gpghome/')


def import_key(file: str, passphrase: str = None):
    with open(file, 'r') as f:
        result = gpg.import_keys(f.read(), passphrase=passphrase)
        print(f'[maild] Loaded key: {result.fingerprints[0]}')
        gpg.trust_keys(result.fingerprints[0], 'TRUST_ULTIMATE')


class EmlHandler:
    def __init__(self):
        import_key('./keys/checker.sk', PASSPHRASE)
        import_key('./keys/admin.pk')

    async def handle_DATA(self, server, session, envelope):
        print(f'[maild] Got mail from {session.peer} {session.host_name}')

        if envelope.mail_from != SENDER_EMAIL:
            print('[maild] Unauthorized sender. Ignoring email.')
            return '550 Unauthorized sender'

        if RECIPIENT_EMAIL not in envelope.rcpt_tos:
            print('[maild] Unauthorized recipient. Ignoring email.')
            return '550 Unauthorized recipient'
        
        decrypted = gpg.decrypt(envelope.original_content, passphrase=PASSPHRASE)

        if not decrypted.ok:
            print('[maild] Decryption failed.')
            return '550 Decryption failed'

        if not decrypted.valid:
            print('[maild] Invalid signature.')
            return '550 Invalid signature'

        if decrypted.username != f'{SENDER_USERNAME} <{SENDER_EMAIL}>':
            print('[maild] Unauthorized decrypted sender. Ignoring email.')
            return '550 Unauthorized decrypted sender'
        
        body = decrypted.data.decode().split('\n\n')[-1].strip()

        if INSTRUCTION_BODY != body:
            print('[maild] No valid body found.')
            return '550 No valid body'
        
        print('[maild] Valid instruction found.')
        return f'250 {FLAG}'




async def run():
    print('[maild] Starting maild server.py')
    print(f'[maild] Current Flag: {FLAG}')
    print(f'[maild] Current Passphrase: {PASSPHRASE}')
    handler = EmlHandler()
    controller = Controller(handler, hostname='0.0.0.0', port=25)
    controller.start()
    await asyncio.Event().wait()


if __name__ == '__main__':
    asyncio.run(run())
