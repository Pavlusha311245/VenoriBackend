echo "Deploy script started"
cd /var/www/html
git pull
composer install --no-plugins --no-scripts
echo "Deploy script finished execution"
