#!/bin/bash

docker run -p 127.0.0.1:8081:8081 -i -t -v $PWD/solve.py:/solve.py pwntools/pwntools python solve.py http://127.0.0.1:9090
