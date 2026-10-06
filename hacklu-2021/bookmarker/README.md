# Bookmarker
- Category: Web
- Difficulty: Hard
- Author: kunte_

## Description
Bookmark your favorite links! Register now!

https://bookmarker.flu.xxx

## Idea
Redirect the admin bot with a flaw in the link shim, leak the flag with a CSP XS-Leak.

## Setup

- build bot docker: `docker build -t bot src/bot`
- start everything: `docker-compose up`
- debug: `NPM_RUN_SCRIPT=dev docker-compose up --build app db`

## Port Mappings

- "8083:80" admin bot
- "8080:8080" app

## Solution
See [solve.html](solution/solve.html).


