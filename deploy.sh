echo "Deploy script started"
cd /var/www/fullplate

echo "=====PULLING====="
git pull origin develop
echo "=====PULLED====="

echo "=====INSTALLING====="
composer install --no-scripts
echo "=====INSTALLED====="

echo "=====MIGRATING====="
php artisan migrate
echo "=====MIGRATED====="

echo "Deploy script finished execution"