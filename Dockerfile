FROM php:8.0-fpm-buster

ENV TZ Asia/Tokyo
RUN echo "${TZ}" > /etc/timezone \
   && dpkg-reconfigure -f noninteractive tzdata

RUN apt-get update && apt-get install -y \
  zlib1g-dev \
  default-mysql-client

COPY ./ ./

ENV PHP_MEMORY_LIMIT 512M
ENV PHP_MAX_EXECUTION_TIME 250