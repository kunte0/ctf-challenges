#!/bin/env bash



echo "***********censored***********" | gpg --homedir /tmp/ --passphrase-fd 0 --pinentry-mode loopback --batch --quick-generate-key "checker <checker@pushgainpull.club>"
echo "" | gpg --homedir /tmp/ --passphrase-fd 0 --pinentry-mode loopback --batch --quick-generate-key "admin <admin@pushgainpull.club>"
gpg --homedir /tmp/ --armor --export-secret-keys --pinentry-mode=loopback --passphrase "***********censored***********" checker@pushgainpull.club > keys/checker.sk
gpg --homedir /tmp/ --armor --export checker@pushgainpull.club > keys/checker.pk
gpg --homedir /tmp/ --armor --export admin@pushgainpull.club > keys/admin.pk