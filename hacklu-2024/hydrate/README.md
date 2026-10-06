# Hydrate Today
- Category: web
- Difficulty: medium
- Author: kunte_

## Description
```html
Grab a Flask and hydrate today!<br><br>
<a href="https://challenge.zip/hydrate_405c8227f43e3076b3f387521467f2d2.zip" download>Download challenge files</a><br><br>
Solve the challenge locally then start an instance to get the flag here: <code>nc hydrate.today 1337</code>
```
## Idea
Looks like xss but nope. Make the bot download flask.py, then crash flask (easy?), docker will restart, from flask import Flask gives you RCE.

## Setup
docker compose up in `src/hydrate`

## Solution
See [solve.py](solution/solve.py).


