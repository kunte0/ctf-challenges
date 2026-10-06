import os
from selenium import webdriver
from selenium.webdriver.chrome.options import Options
from time import sleep


def visit(url):
    if not url.startswith(('http://', 'https://')):
        raise ValueError('Invalid URL.')
    options = Options()
    options.add_argument('--headless')

    # a few things have to go sorry
    options.add_argument('--disable-extensions')
    options.add_argument('--disable-gpu')
    options.add_argument('--disable-software-rasterizer')
    options.add_argument('--disable-dev-shm-usage')

    # fix downloads
    prefs = {
        "download.default_directory": os.getcwd(),
        "safebrowsing.enabled": "true"
    }
    options.add_experimental_option("prefs", prefs)

    # docker docker docker
    options.add_argument('--no-sandbox')
    # no exploits
    options.add_argument('--js-flags=--noexpose_wasm,--jitless')

    driver = webdriver.Chrome(options=options)

    driver.get(url)
    sleep(3)
    
    driver.close()
    driver.quit()