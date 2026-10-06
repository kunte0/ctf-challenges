FROM python:3-alpine

RUN apk update \
    # Install browser
    && apk add chromium chromium-chromedriver curl \
    # Install python dependencies
    && pip install flask selenium

COPY app/ /app/

WORKDIR /app/

CMD ["python", "app.py"]