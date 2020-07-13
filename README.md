## Update node js

`sudo npm cache clean -f`

`sudo npm install -g n`

`sudo n stable`

## Installing puppeteer

`sudo npm install puppeteer --global`

or

`sudo npm install -g puppeteer --unsafe-perm=true --allow-root`

## Add SVG image support to Imagick

`sudo apt-get install libmagickcore-6.q16-3 libmagickcore-6.q16-3-extra`

## Adding cron command

Open cron file editing:

`crontab -e`

and insert the text

`* * * * * cd ~/sites-reviews.com && php artisan schedule:run >> /dev/null 2>&1`

## Publish storage

`php artisan storage:link`
