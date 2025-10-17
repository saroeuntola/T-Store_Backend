
how to set up project  <br/>

rename env.example to .env  <br/>
create your database name and put database name into the .env  <br/>

run cmd:  <br/>
composer install  <br/>
php artisan key:generate  <br/>
php artisan migrate  <br/>
php artisan db:seed --class=PermissionSeeder  <br/>
php artisan db:seed --class=AdminSeeder  <br/>
php artisn serve 

