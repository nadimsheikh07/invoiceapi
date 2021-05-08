# web services

## Setup

clone the repo and then run `composer install`.

### Create Database
```bash
php artisan db:create
php artisan db:create --env=dev
```
### delete all table
```bash
php artisan db:wipe
php artisan db:wipe --env=dev
```

### Migrations

Run the following command to run startup migrations.

```bash
php artisan migrate --force
php artisan migrate --force --env=dev
```

### Seed

Run the following command to run startup seeds.

```bash
php artisan db:seed --force
php artisan db:seed --force --env=dev
```

### Run Application

Run the following command to run application.

```bash
php artisan serve
php artisan serve --env=dev
```
