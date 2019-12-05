## Smartlyjobs

Smartlyjobs API is a new management application

This version is built on Laravel 5.7!

It is built with packages:

* JWT-Auth - [tymondesigns/jwt-auth](https://github.com/tymondesigns/jwt-auth)
* Laravel-CORS [barryvdh/laravel-cors](http://github.com/barryvdh/laravel-cors)
* Laravel Swagger [jlapp/swaggervel](https://github.com/slampenny/Swaggervel)
* Laravel Permissions [spatie/laravel-permission](https://github.com/spatie/laravel-permission)

## Installation

1. run `git clone git@bitbucket.org:smartlyjobs/smartlyjobs-backend.git`;
2. run `cp .env.example .env`;
3. change settings in .env file;
4. run `composer install`;
4. run `php artisan key:generate`;
5. run `docker-compose up`

Once the project creation procedure will be completed, 
run the `docker-compose exec php php artisan migrate --seed` command 
to install the required tables.

### Deployment guidelines ###

* Dev branch: `develpment` is deployed to: http://tempest-dev.eu-west-2.elasticbeanstalk.com/
    
* Prod branch: `master` is deployed to: !missing production url!
    
### Contribution guidelines ###

* Writing tests
    * check phpunit documentation

* Code review
    * please create pull requests for every new feature

* Other guidelines
* For changelog we use https://github.com/rafinskipg/git-changelog
    * command is: `git-changelog -t false -a "Tempest APP API"`
    * Commit messages format: [conventional changelog](https://github.com/conventional-changelog/conventional-changelog/tree/master/packages/conventional-changelog-angular)
	
