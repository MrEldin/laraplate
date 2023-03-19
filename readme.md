## Laraplate

!!!WIP!!!

Laraplate is Laravel API boilerplate

This version is built on Laravel 9!

It is built with packages:

* JWT-Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* Laravel Permissions [spatie/laravel-permission](https://github.com/spatie/laravel-permission)
* L5-repository [andersao/l5-repository](https://github.com/andersao/l5-repository)

## Installation

1. run `git clone https://github.com/MrEldin/laraplate.git .`;
2. run `cp .env.example .env`;
3. change settings in .env file;
4. run `composer install`;
5. run `php artisan key:generate`;
6. run `cp -a /Docker/. /`
7. run `docker-compose up`
8. run `docker-compose exec php bash`

Once the project creation procedure will be completed, 
run the `php artisan migrate --seed` command 
to install the required tables.
    
### Contribution guidelines ###

* Writing tests
    * check phpunit documentation

* Code review
    * please create pull requests for every new feature

* Other guidelines
