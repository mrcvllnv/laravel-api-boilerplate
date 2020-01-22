# Laravel API Boilerplate

## Installation

Clone the repo locally:

```sh
git clone https://github.com/mrcvllnv/laravel-api-boilerplate.git
cd laravel-api-boilerplate
```

Install PHP dependencies:

```sh
composer install
```

Setup configuration:

```sh
cp .env.example .env
```

Generate application key:

```sh
php artisan key:generate
```

Generate jwt secret key:

```sh
php artisan jwt:secret
```

Create an SQLite database. You can also use another database (MySQL, Postgres), simply update your configuration accordingly.

```sh
touch database/database.sqlite
```

Run database migrations:

```sh
php artisan migrate
```

Run database seeder:

```sh
php artisan db:seed
```

Run the dev server (the output will give the address):

```sh
php artisan serve
```

## Running tests

To run the tests, run:

```sh
vendor/bin/phpunit
```