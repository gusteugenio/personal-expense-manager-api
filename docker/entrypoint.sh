#!/bin/sh
set -e

mkdir -p /var/www/html/runtime/logs /var/www/html/web/assets
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets
chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

until php -r "
require '/var/www/html/vendor/autoload.php';
try {
  new PDO('mysql:host=' . getenv('DB_HOST') . ';port=' . getenv('DB_PORT'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'));
} catch (Exception \$e) {
  exit(1);
}
" 2>/dev/null; do
  echo "Aguardando banco de dados..."
  sleep 2
done

php /var/www/html/yii migrate --interactive=0

PORT="${PORT:-80}"
sed -i "s/Listen 80/Listen ${PORT}/" /etc/apache2/ports.conf
sed -i "s/:80/:${PORT}/" /etc/apache2/sites-available/000-default.conf

a2dismod mpm_event mpm_worker || true
rm -f /etc/apache2/mods-enabled/mpm_event.* /etc/apache2/mods-enabled/mpm_worker.*
a2enmod mpm_prefork
apache2ctl -t

exec apache2-foreground
