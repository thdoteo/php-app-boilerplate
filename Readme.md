# php-framework

A PHP framework to start developping apps faster.

## Usage

Start `server.sh` to launch PHP's developpement web server and open your browser on `http://localhost:8000`.

## Features

- A scalable structure (POO, Requests/Responses, Actions, Middlewares, Entities, Tables)
- [Composer](https://github.com/composer/composer) for handling dependencies
- A router relying on [Zend Expressive FastRoute](https://github.com/zendframework/zend-expressive-fastroute)
- Independent modules (Blog, Auth, Admin...)
- A development (with [Whoops](https://github.com/filp/whoops) for debugging) and production mode (with caching) 
- A flexible configuration with [PHP-DI](https://github.com/PHP-DI/PHP-DI)
- Migrations and seedings of the database with [Phinx](https://github.com/cakephp/phinx)
- A tough code with [PHPUnit](https://github.com/sebastianbergmann/phpunit)
- A nice looking code with a [PHP Code Sniffer](https://github.com/squizlabs/PHP_CodeSniffer) to respect PSR-15

## Fixes

- Remove admin.widgets in Admin config
- Add Site module
- Handle 404/500 HTTP errors
- Query: add alias for join method
- Validator: add URL, make some rules valid if empty
- Form: select, password
- &session bug in php-di cache
- Escape table names in PDO queries

# To-Do

- Improve documentation and tests (for middlewares)
- Add other modules (Shop...)
