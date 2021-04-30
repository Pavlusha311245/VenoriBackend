echo "Deploy script started"
cd /var/www/fullplate

echo "=====PULLING====="
git reset --hard origin/develop
git pull origin develop
echo "=====PULLED====="

echo "=====INSTALLING====="
composer install
echo "=====INSTALLED====="

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
