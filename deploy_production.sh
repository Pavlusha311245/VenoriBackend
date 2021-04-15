echo "Deploy script started"
cd /var/www/html

echo "=====PULLING====="
git reset --hard origin/master
git pull origin master
echo "=====PULLED====="

echo "=====INSTALLING====="
composer install
echo "=====INSTALLED====="

echo "=====MIGRATING====="
php artisan migrate
echo "=====MIGRATED====="

echo "Deploy script finished execution"
