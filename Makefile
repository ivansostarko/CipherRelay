SHELL := /bin/bash

up:
\tdocker compose up -d --build

down:
\tdocker compose down

restart: down up

logs:
\tdocker compose logs -f --tail=200

ps:
\tdocker compose ps

composer-install:
\tdocker compose run --rm app composer install

key-generate:
\tdocker compose run --rm app php artisan key:generate

migrate:
\tdocker compose run --rm app php artisan migrate

seed:
\tdocker compose run --rm app php artisan db:seed --class=AdminSeeder

seed-demo:
\tdocker compose run --rm app php artisan db:seed --class=DemoDataSeeder

storage-link:
\tdocker compose run --rm app php artisan storage:link

permit:
\tchmod -R 775 storage bootstrap/cache || true
