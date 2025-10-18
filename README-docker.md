# Docker Setup for CipherRelay

This Compose stack provides everything to run the Laravel app:

- **app**: PHP 8.3 FPM with Composer and extensions (pdo_mysql, mbstring, zip, intl, pcntl, redis, sodium)
- **web**: Nginx serving `/public`
- **mysql**: MySQL 8.0
- **redis**: Redis 7
- **queue**: Laravel queue worker (`queue:work`)
- **scheduler**: Laravel scheduler (`schedule:work`)

## Quickstart

1) Put this folder at your project root (where `artisan` lives).  
2) Copy env and adjust:
```bash
cp .env.docker.example .env
```
3) Build & start:
```bash
make up
```
4) Install deps & app key:
```bash
make composer-install
make key-generate
```
5) Migrate & seed:
```bash
make migrate
make seed
# optional demo data:
make seed-demo
```
6) Storage symlink:
```bash
make storage-link
```

App URL: **http://localhost:8080**  
MySQL: `localhost:33060` (host port) user: `laravel` / pass: `laravel`  
Redis: `localhost:6379`

## Notes

- Source is mounted into containers (`.:/var/www/html`). Composer installs into your host `vendor/`.
- File upload limit is `64M` in both PHP and Nginx. Adjust in:
  - `docker/php/Dockerfile` env vars
  - `docker/nginx/conf.d/app.conf` (`client_max_body_size`)
- If you hit permission issues on Linux hosts, try:
  ```bash
  make permit
  ```
  Or change container user to match your host UID in the Dockerfile.
