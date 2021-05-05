echo "Deploy script started"
cd /var/www/venori

echo "=====PULLING====="
git reset --hard origin/master
git pull origin master

echo "=====INSTALLING====="
composer install --optimize-autoloader --no-dev

echo "=====MIGRATING====="
php artisan migrate --seed

echo "=====CONFIGURATION====="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "======GENERATING DOC======"
php artisan l5-swagger:generate

echo "======OPTIMIZE======"
php artisan optimize

echo "Deploy script finished execution"
