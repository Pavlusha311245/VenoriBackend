echo "Deploy script started"
cd /var/www/venori

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

echo "======GENERATING DOC======"
php artisan l5-swagger:generate
echo "======GENERATED======"

echo "======OPTIMIZE======"
php artisan optimize
echo "======OPTIMIZED======"

echo "Deploy script finished execution"
