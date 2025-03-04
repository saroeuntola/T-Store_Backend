php artisan db:wipe


php artisan migrate
php artisan db:seed --class=PermissionSeeder
php artisan db:seed --class=AdminSeeder

php artisan key:generate

php artisan serve