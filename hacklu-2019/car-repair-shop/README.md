car-repair-shop
=======

# Author
kunte_ (reviewer: pspaul)

# Description
Prototype pollution into a script-src filter bypass.
`$.extend(true, this, JSON.parse(urlParams.get('repair')))` deep-merges attacker
JSON, so `{"__proto__":{"__proto__":["lol"]}}` puts `Array.prototype` in the
chain and `porsche` stringifies to `lol`, passing its md5 check. That reaches
`repairWithHelper`, whose regex only demands `\w{4,5}://<host>/<x>/....js` —
`data` is four characters, so `data://<host>/a/,alert(1337)//.js` loads as a
script with the trailing `.js` commented out.

# Setup
nginx serving the static page in [src/](src/), plus an admin bot. The flag is in
the bot's cookie.

# Solution
See [solution.md](solution/solution.md) for the payloads.

# Point Value
446

# Flag
flag{brumm_brumm_brumm_brumm_brumm_brumm_brumm}
