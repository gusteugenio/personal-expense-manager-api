#!/bin/sh
set -e

# Garante permissões de escrita mesmo com o volume montado por cima da imagem
mkdir -p /var/www/html/runtime/logs /var/www/html/web/assets
chown -R www-data:www-data /var/www/html/runtime /var/www/html/web/assets
chmod -R 775 /var/www/html/runtime /var/www/html/web/assets

# Aguarda o banco de dados aceitar conexoes antes de continuar
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
php /var/www/html/yii seed

exec apache2-foreground
