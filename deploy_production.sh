echo "Deploy script started"
cd /var/www/venori

echo "=====PULLING====="
git reset --hard origin/master
git pull origin master
echo "=====PULLED====="

echo "=====INSTALLING====="
composer install --optimize-autoloader --no-dev
echo "=====INSTALLED====="

echo "=====CONFIGURATION====="
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo "=====CONFIGURED====="

echo "=====MIGRATING====="
php artisan migrate
echo "=====MIGRATED====="

echo "======GENERATING DOC======"
php artisan l5-swagger:generate
echo "======GENERATED======"

echo "======OPTIMIZE======"
php artisan optimize
echo "======OPTIMIZED======"

echo "Deploy script finished execution"
