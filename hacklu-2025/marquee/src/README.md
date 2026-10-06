# Bot


change the url to the one you want to visit in docker-compose.yml
```
docker compose up --build
```
or build and run the docker container with the following command
```
docker build -t bot .
docker run -i --rm bot 'http://your-url-here'
```

see the Dockerfile and docker-compose.yml for more information



# TagDeprecater Extension

Go to chrome://extensions/ and enable developer mode, then click on load unpacked and select the extension folder.

Please don't install the extension in your main browser, it is not safe :)