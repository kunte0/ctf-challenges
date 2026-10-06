#!/bin/bash

echo -e "GET http://localhost:a/ HTTP/1.1\r\nHOST: localhost\r\n\r\n"| nc -vvv 127.0.0.1 9090

