# Päääd
- Category: Web
- Difficulty: Hard
- Author: kunte_

## Description
Are you using a Päääd to take notes during this CTF? Why not use this Päääd instead?

## Idea
xs-leak

## Setup

- see .env
- start everything: `docker-compose up`
- debug and without bot: `NPM_RUN_SCRIPT=dev docker-compose up --build app db`

## Port Mappings

- "8083:80" admin bot
- "9876:9876" app

## Solution
See [exploitpäääd.html](solution/exploitpäääd.html).


