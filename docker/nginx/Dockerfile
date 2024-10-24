FROM nginx:1.24.0-alpine
# https://github.com/nginxinc/docker-nginx/blob/master/mainline/alpine/Dockerfile

COPY ./docker/nginx/local.conf /etc/nginx/conf.d/default.conf
COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf

VOLUME /var/www/public