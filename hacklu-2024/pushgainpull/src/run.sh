rm -rf public/.git
rm public/__init__.py
rm public/keys/admin.pk
rm public/keys/checker.sk
rm public/spoofed.eml



docker build -t context .
docker run --rm -v $(pwd)/public:/src context
