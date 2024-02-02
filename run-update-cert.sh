#!/bin/sh

cp /var/www/html/zimbra-api/cert/* /usr/local/share/ca-certificates
echo "192.168.100.100 mail.hcm.abc" >> /etc/hosts

echo "Note: It is important to have the .crt extension on the file, otherwise it will not be processed."
update-ca-certificates

sed -i 's|AllowOverride None|AllowOverride All|g' /etc/apache2/apache2.conf

chmod 775 /var/www/html

echo "Succecfully !"

supervisord -c /etc/supervisord.conf
