echo "Deploy script started"
cd /var/www/fullplate

git pull origin develop
echo "=====PULLED====="
composer install --no-scripts
echo "=====INSTALLED====="
php artisan optimize
echo "=====OPTIMIZED====="
php artisan migrate
echo "=====MIGRATED====="
echo "Deploy script finished execution"