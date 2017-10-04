#--------------------------------------------------------------------------
# Image Setup
#--------------------------------------------------------------------------
# To edit the 'php-fpm' base Image, visit its repository on Github
#    https://github.com/LaraDock/php-fpm
# To change its version, see the available Tags on the Docker Hub:
#    https://hub.docker.com/r/laradock/php-fpm/tags/

FROM laradock/php-fpm:7.1--1.3

MAINTAINER Popov Argir <argirco.popov@gmail.com>

LABEL Description="Laravel PHP-FPM Image"

RUN apt-get update && apt-get install -y libzip-dev ssh-client poppler-utils ghostscript pkg-config imagemagick libmagickwand-dev libxml2-dev git && \
    pecl install xdebug zip imagick && \
    docker-php-ext-enable xdebug zip imagick && \
    docker-php-ext-install bcmath soap

# Install composer and add its bin to the PATH.
RUN curl -s http://getcomposer.org/installer | php && \
    echo "export PATH=${PATH}:/var/www/vendor/bin" >> ~/.bashrc && \
    mv composer.phar /usr/bin/composer && \
    /usr/bin/composer global require hirak/prestissimo && \
    /usr/bin/composer global require laravel/envoy

RUN . ~/.bashrc
#
#--------------------------------------------------------------------------
# Final Touch
#--------------------------------------------------------------------------
#

# Copy xdebug configration for remote debugging
COPY ./xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

ADD ./laravel.ini /usr/local/etc/php/conf.d

RUN rm -r /var/lib/apt/lists/*

RUN usermod -u 1000 www-data && \
    sed -i -e 's/max_children\s=\s[0-9]/max_children = 100/g' /usr/local/etc/php-fpm.d/www.conf

WORKDIR /var/www
COPY entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh
ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]

EXPOSE 9000