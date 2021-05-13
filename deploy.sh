echo "Deploy script started"
cd /var/www/fullplate

echo "=====PULLING====="
git reset --hard origin/develop
git pull origin develop

echo "=====INSTALLING====="
composer install

echo "=====MIGRATING====="
php artisan migrate --seed

echo "=====CONFIGURATION====="
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "=====SET STORAGE LINK====="
php artisan storage:link

echo "======GENERATING DOC======"
php artisan l5-swagger:generate

echo "======OPTIMIZE======"
php artisan optimize

echo "Deploy script finished execution"
