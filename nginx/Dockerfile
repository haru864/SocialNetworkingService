FROM nginx:1.26.1

COPY ./nginx/conf/nginx.conf /etc/nginx/nginx.conf
COPY ./nginx/conf/default.conf /etc/nginx/conf.d/default.conf
COPY ./nginx/conf/sns.conf /etc/nginx/conf.d/sns.conf

EXPOSE 80
