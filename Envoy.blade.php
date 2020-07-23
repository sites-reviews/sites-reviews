@servers(['web' => ['litlife@62.210.157.216']])

@task('deploy', ['on' => 'web'])
cd sites-reviews.com
php artisan down --message="Сейчас мы обновляем движок сайта. Пожалуйста, зайдите чуть позже.." --retry=600 --allow=127.0.0.1,136.0.0.75
php artisan optimize:clear
git pull origin master
php -d memory_limit=-1 composer.phar install --no-dev
php artisan migrate --force
php artisan optimize
php artisan up
@endtask

@task('deploy-force', ['on' => 'web'])
cd sites-reviews.com
php artisan down --message="Сейчас мы обновляем движок сайта. . Пожалуйста, зайдите чуть позже.." --retry=60 --allow=127.0.0.1
php artisan optimize:clear
git reset --hard
git pull origin master
php -d memory_limit=-1 composer.phar install --no-dev
php artisan migrate --force
php artisan optimize
php artisan up
@endtask

@task('geoip-update', ['on' => 'web'])
cd sites-reviews.com
php artisan down --message="Обновление geoip" --retry=60  --allow=127.0.0.1
php artisan geoip:update
php artisan up
@endtask

@task('deploy-light', ['on' => 'web'])
cd sites-reviews.com
git reset --hard
git pull origin master
php artisan optimize
@endtask

@task('composer-update', ['on' => 'web'])
cd sites-reviews.com
php artisan down --message="Сейчас мы обновляем движок сайта. Пожалуйста, зайдите чуть позже.." --retry=600 --allow=127.0.0.1
php artisan optimize:clear
php -d memory_limit=-1 composer.phar install --no-dev
php artisan optimize
php artisan up
@endtask

@task('migrate', ['on' => 'web'])
cd sites-reviews.com
git reset --hard
php artisan down --message="Проходит обновление базы данных" --allow=127.0.0.1
git pull origin master
php artisan migrate --force
php artisan optimize
php artisan up
@endtask

@task('dump-autoload', ['on' => 'web'])
cd sites-reviews.com
php artisan down --message="Сейчас мы обновляем движок сайта. Пожалуйста, зайдите чуть позже.." --retry=600 --allow=127.0.0.1,136.0.0.75
php artisan optimize:clear
php -d memory_limit=-1 composer.phar dump-autoload --optimize
php artisan optimize
php artisan up
@endtask
