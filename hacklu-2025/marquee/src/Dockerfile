FROM node:24-alpine

RUN apk update && apk add chromium 

WORKDIR /app/
COPY . /app/

ENV PUPPETEER_EXECUTABLE_PATH=/usr/bin/chromium-browser
ENV PUPPETEER_SKIP_DOWNLOAD=true

RUN ["npm", "install"]


EXPOSE 3000
ENTRYPOINT ["node", "bot.js"]