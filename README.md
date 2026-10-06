# My CTF challenges
Challenges I made for [Hack.lu CTF](https://flu.xxx), run by [FluxFingers](https://fluxfingers.net). Also see the [FluxFingers CTF archive](https://archive.fluxfingers.net/).

- Hack.lu CTF 2018
    - [IDeaShare (Web 500)](hacklu-2018/ideashare/): Double bypass of PHPIDS against an idea-sharing app whose admin page is localhost-only.
    - [Baby PHP (Web 153)](hacklu-2018/baby-php/): A combination of PHP quirks: `php://input`, `intval` vs `===`, a full-width `＄` smuggled past the regex, and variable variables.
- Hack.lu CTF 2019
    - [Car Repair Shop (Web 446)](hacklu-2019/car-repair-shop/): XSS via prototype pollution plus a `data:` URI script bypass.
- Hack.lu CTF 2020
    - [Secret Image Sharing (Web 401)](hacklu-2020/secret-image-sharing/): Upload type confusion into stored XSS, cookie tossing, then an XS-search whose oracle only works because a service worker rewrites the request to a credentialed POST.
    - [Litter Box (Web 500)](hacklu-2020/litter-box/): Minimal postMessage XSS challenge with one `eval()` behind `e.source == window.frames[0]`.
- Hack.lu CTF 2021
    - [Bookmarker (Web 333)](hacklu-2021/bookmarker/): Redirect the admin bot through a flaw in the link shim, then leak the flag with a CSP-based XS-Leak.
    - [Diamond Safe (Web 180)](hacklu-2021/diamond-safe/): WordPress-style SQL injection reached through a GET parameter parsing quirk, escalated to LFI.
- Hack.lu CTF 2022
    - [FoodAPI (Web 500)](hacklu-2022/foodapi/): Desync denodb's prepared statement with a query parameter named `?` for SQLi, then read the oracle cross-site with an XS-Leak.
- Hack.lu CTF 2023
    - [Päääd (Web 405)](hacklu-2023/paaad/): Leak the admin's secret pad subdomain by framing `/p/latest` and reading the blocked frame's CSP violation report out of the Resource Timing API, then flip the pad public with a stored `<meta refresh>` the Sanitizer API lets through.
- Hack.lu CTF 2024
    - [EquipTracker (Web 478)](hacklu-2024/equiptracker/): XS-search but it needs cookies in an iframe, against Firefox with third-party cookies blocked. Enable them by using Firefox's redirect heuristic via a Traefik dashboard open redirect.
    - [Hydrate Today (Web 428)](hacklu-2024/hydrate/): Looks like XSS, but isn't. Make the bot download a `flask.py`, crash the app so Docker restarts it, and `from flask import Flask` is your RCE.
    - [Push Gain Pull (Crypto 428)](hacklu-2024/pushgainpull/): OpenPGP signatures have no context. Re-wrap an admin's signed commit (`git commit -S "Give Flag"`) as an OpenPGP literal and the mail service verifies it as a signed email from admin.
- Hack.lu CTF 2025
    - [BILLY BOARD (Web 453)](hacklu-2025/billyboard/): Opossum attack: Apache with `SSLEngine Optional`. Sending `Upgrade: TLS/1.0` on port 80 lets a MITM desync the response stream, rendering the attacker's session on the real HTTPS origin, so self-XSS becomes XSS.
    - [XOD (Web 299)](hacklu-2025/xod/): XSS delivered through a DNS-over-HTTPS response (XSS-over-DoH), then also bypass the CSP with a DoH response.
    - [MÅRQUEE (Web 86)](hacklu-2025/marquee/): Browser extension with uXSS, beginner challenge.


