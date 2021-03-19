echo "Deploy script started"
cd /var/www/html
git pull
echo "=====PULLED====="
composer install --no-scripts
echo "=====INSTALLED====="
php artisan migrate
echo "=====MIGRATED====="
php artisan optimize
echo "=====OPTIMIZED====="
echo "Deploy script finished execution"
