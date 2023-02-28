# Laravel GraphQL Example

[Link to article described this code](https://medium.com/@thepatrykooo/laravel-graphql-project-sample-cc9c7236c7b1)

## Introduction

I have prepared a Laravel project with using GraphQL [https://lighthouse-php.com/](https://lighthouse-php.com/)

## Business Features

- Authorization user using Sanctum
- Implement CRUD functionality for managing company departments
- Implement CRUD functionality for managing employees assigned to departments

## Run project

Clone repository:

    git clone git@github.com:ThePatrykOOO/laravel-graphql-example.git

Go to folder:

    cd laravel-graphql-example

Install dependencies:

    composer install

Copy .env file:

    cp .env.example .env

Generate app key:

    php artisan key:generate

Run migrations:

    php artisan migrate

Run tests (optional):

    ./vendor/bin/phpunit
