echo "Deploy script started"
cd /var/www/html

echo "=====PULLING====="
git fetch --all
git reset --hard origin/develop
git pull origin develop
echo "=====PULLED====="

echo "=====INSTALLING====="
composer install
echo "=====INSTALLED====="

echo "=====MIGRATING====="
php artisan migrate --force
echo "=====MIGRATED====="

echo "Deploy script finished execution"