
how to set up project

rename env.example to .env 
create your database name and put database name into the .env

run cmd:
composer install
php artisan key:generate
php artisan migrate
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=AdminSeeder
php artisn serve

