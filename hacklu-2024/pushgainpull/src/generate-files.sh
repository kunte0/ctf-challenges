#!/bin/env sh

cd /src

echo "rJDkvWUStZJJDQtWDrkY" | gpg --passphrase-fd 0 --pinentry-mode loopback --batch --quick-generate-key "checker <checker@pushgainpull.club>"
echo "" | gpg --passphrase-fd 0 --pinentry-mode loopback --batch --quick-generate-key "admin <admin@pushgainpull.club>"

gpg --armor --export-secret-keys --pinentry-mode=loopback --passphrase "rJDkvWUStZJJDQtWDrkY" checker@pushgainpull.club > keys/checker.sk
gpg --armor --export checker@pushgainpull.club > keys/checker.pk
gpg --armor --export admin@pushgainpull.club > keys/admin.pk



GPG_KEY_ID=$(gpg --list-secret-keys --with-colons "admin@pushgainpull.club" | grep '^sec' | cut -d: -f5)



git init
git config --local user.name "admin"
git config --local user.email "admin@pushgainpull.club"
git config --local user.signingkey "$GPG_KEY_ID"
git config --local commit.gpgSign true
git add .
git commit -S -m "Initial commit"
echo "# get the flag" > __init__.py
git add __init__.py
git commit -S -m "Give Flag"

: '
git cat-file commit HEAD | sed -e 's/^ //'| sed -e 's/gpgsig //' | gpg --dearmor > /tmp/signature.bin
git verify-commit -v HEAD | gpg -z0 --store > /tmp/plaintext.bin
# cat /tmp/signature.bin /tmp/plaintext.bin | gpg --enarmor > /tmp/spoofed-content.pgp
# sed -e 's/ARMORED FILE/MESSAGE/' \
#     -e '/^Comment:/d' /tmp/spoofed-content.pgp > /tmp/spoofed-mime.txt
cat /tmp/signature.bin /tmp/plaintext.bin | gpg -r checker@pushgainpull.club --no-literal --encrypt --armor > /tmp/spoofed-mime.txt
# TIMESTAMP=$(gpg --status-fd=1 --decrypt /tmp/spoofed-mime.txt | grep VALIDSIG | cut -d' ' -f5)
cat << EOF > /tmp/spoofed.eml
From: admin <admin@pushgainpull.club>
To: Checker <checker@pushgainpull.club>
Subject: Give Flag
MIME-Version: 1.0
Content-Type: multipart/encrypted;
 protocol="application/pgp-encrypted";
 boundary="XXX"

This is an OpenPGP/MIME encrypted message.
--XXX
Content-Type: application/pgp-encrypted

Version: 1

--XXX
Content-Type: application/octet-stream; name="encrypted.asc"
Content-Disposition: inline; filename="encrypted.asc"

EOF

cat /tmp/spoofed-mime.txt >> /tmp/spoofed.eml
cat <<EOF >> /tmp/spoofed.eml

--XXX--
EOF


cat /tmp/spoofed.eml > spoofed.eml

'