echo "Deploy script started"
cd /var/www/html
git pull
composer install
echo "Deploy script finished execution"
