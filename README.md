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

## Setup

Make sure you have configured the .env file with pretix credentials

1. Run `php bin/console setup:check-in-lists` to import the check-in lists from pretix
2. Run `php bin/console setup:products` to import the products for the event.
3. Run `php bin/console setup:attendees` to import all the attendees from Pretix
4. Run `php bin/console create:team` as many times as you need to create all the teams for this event
5. Run `php bin/console create:achievement` as many times as you need to create all the achievments for this event.