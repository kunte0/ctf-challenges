# Litter Box
- Category: web
- Difficulty: hard
- Author: kunte_

## Description
Do you like Cats? Do you like XSS? If you answered "Yes" to one of these
questions, this challenge is for you: https://litterbox.cf

Proof you have XSS by stealing the cookie (sameSite:None and secure) from the
admin.

## Idea
The whole challenge is three static files. `index.html` evals any message whose
sender passes one check:

```js
window.onmessage = (e) => {
    if (e.source == window.frames[0]){
            eval(e.data) // 😽
    }
}
```

The iframe is `sandbox`ed without `allow-scripts`, so it can never post
anything itself. The check is loose equality, so `null == undefined` passes:
destroy the window that sent the message and `e.source` becomes `null`; catch
the page before `main.js` has set up the iframe and `window.frames[0]` is
`undefined`.

## Setup
Static files, any webserver + admin bot

## Solution
See [solve.html](solution/solve.html). It exhausts the
connection pool with 256 sleeping subdomains so `main.js` cannot load and the
iframe is never parsed, then repeatedly creates an iframe, postMessages from it
to `parent.frames[0]`, and deletes it immediately so `e.source` arrives as
`null`. `run()` is commented out at the bottom.

See also [a player's writeup](https://krial057.github.io/blog/hack_lu_litter_box),
which gets there by racing the script load instead.


