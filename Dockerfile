FROM ubuntu

WORKDIR /var/www/html/
RUN mkdir -p /var/www/html/zimbra-api && \
    mkdir -p /etc/supervisor/conf.d
COPY zimbra-api /var/www/html/zimbra-api

RUN apt update && \
    apt-get install -yq tzdata && \
    ln -fs /usr/share/zoneinfo/Asia/Ho_Chi_Minh /etc/localtime && \
    dpkg-reconfigure -f noninteractive tzdata

RUN apt install -y apache2
RUN apt install -y apache2-utils 
RUN apt install -yq php php-cli php-fpm php-mysqlnd php-zip php-gd php-mbstring php-curl php-xml php-pear php-bcmath php-json composer
RUN apt clean
RUN composer require myclabs/php-enum jms/serializer php-http/discovery zimbra-api/soap-api nyholm/psr7 php-http/curl-client guzzlehttp/psr7 php-http/message 
RUN apt-get install -y ca-certificates supervisor curl

COPY ./supervisor-apache2.conf /etc/supervisor/conf.d/apache2.conf
COPY ./supervisord.conf /etc/supervisord.conf
COPY ./run-update-cert.sh /opt/run-update-cert.sh

RUN chmod +x /opt/run-update-cert.sh

ENV TZ="Asia/Ho_Chi_Minh"


EXPOSE 80



CMD ["sh","/opt/run-update-cert.sh"]
