FROM python:3.12-slim AS circus-docs
WORKDIR /app

# build python deps
COPY docs/requirements.txt /app/
RUN pip install -r requirements.txt

COPY docs /app

RUN sphinx-build source _site

FROM node:21.6-slim AS circus-webpack
WORKDIR /app

RUN apt-get update \
    && apt-get install -y git \
    && npm upgrade -g npm \
    && npm upgrade -g yarn \
    && rm -rf /var/lib/apt/lists/*

# build js deps
COPY public/package.json public/yarn.lock public/webpack.config.js /app/
RUN yarn

# run webpack
COPY public /app
RUN yarn webpack


FROM circus-webpack AS circus-prod-assets

RUN yarn --production \
    && yarn cache clean

# build js deps
COPY public/package.json public/yarn.lock /app/

RUN yarn --production \
    && yarn cache clean


FROM dhilsfu/symfony-base:php-8.2-apache AS circus
ENV GIT_REPO=https://github.com/sfu-dhil/circus

# basic deps installer (no script/plugings)
COPY --chown=www-data:www-data --chmod=775 composer.json composer.lock /var/www/html/
RUN composer install --no-scripts

# copy project files and install all symfony deps
COPY --chown=www-data:www-data --chmod=775 . /var/www/html
# copy webpacked css and libs
COPY --chown=www-data:www-data --chmod=775 --from=circus-prod-assets /app/css /var/www/html/public/css
COPY --chown=www-data:www-data --chmod=775 --from=circus-prod-assets /app/node_modules /var/www/html/public/node_modules
# copy docs
COPY --chown=www-data:www-data --chmod=775 --from=circus-docs /app/_site /var/www/html/public/docs/sphinx

RUN mkdir -p data/prod data/dev data/test var/cache/prod var/cache/dev var/cache/test var/sessions var/log \
    && chown -R www-data:www-data data var \
    && chmod -R 775 data var \
    && composer install \
    && ./bin/console cache:clear