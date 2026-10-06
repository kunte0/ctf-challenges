# EquipTracker
- Category: web
- Difficulty: hard
- Author: kunte_

## Description
```html
Track your equipment with EquipTracker! This web application allows you to track and manage your gym inventory. One special user has added some interesting equipment, can you get it?

<a href="https://challenge.zip/equiptracker_80a59c29f24eae96b05a8f9ce61b7657.zip" download>Download challenge files</a><br><br>

Solve the challenge locally, then start an instance to get the flag here: <code>nc equiptracker.fit 1337</code>
```
## Idea
XS-search but you need cookies in an iframe. However 3pc-blocking is enabled in Firefox. Just trigger the heuristic kekw. Uses Traefik dashboard open redirect I need to report.

## Setup
docker compose up in `src/equiptracker`

## Solution
See [solve.py](solution/solve.py).


