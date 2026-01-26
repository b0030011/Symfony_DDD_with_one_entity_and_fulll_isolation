FROM nginx:1.22.1
RUN apt-get update && apt-get install -y nginx-extras mc && rm /etc/nginx/sites-enabled/*
COPY default.conf /etc/nginx/conf.d/default.conf