#/var/www/html
php artisan down
git pull origin develop
composer install --no-dev --prefer-dist
php artisan optimize
php artisan migrate
php artisan up
echo 'Deploy finished.'
