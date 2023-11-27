# PonyCon Holland - Backend Service
The backend for the [PonyCon Holland App](https://github.com/Stichting-Dutch-Pony-Events/pch-app)
## Installation
1. Clone this repository
2. Run `composer install`
3. Copy `.env.dist` to `.env` and enter the correct values
4. Run `php bin/console doctrine:database:create`
5. Run `php bin/console doctrine:migrations:migrate`
6. Run `php bin/console lexik:jwt:generate-keypair`
7. All done!
