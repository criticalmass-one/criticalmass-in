# criticalmass.in-symfony

## Requirements

You need the following shit:

* PHP 5.5+
* a mysql installation
* skills in creating tls certificates and webserver configuration

## Installation

* clone the repository
* decide which branch you wanna use, maybe you want to checkout `working` first
* switch into the `symfony` directory
* just to be sure: call `../composer.phar self-update`
* install symfony and all other vendors: `../composer.phar install`
* be sure to have `./app/logs` and `./app/cache` writeable
* put your database credentials into `./app/config/parameters.yml`
* create your schema: `./app/console doctrine:schema:update --force`
* dump all assets: `./app/console assetic:dump`
* add configuration into your `/etc/hosts` that redirects `criticalmass.cm`, `www.criticalmass.cm` and `beta.criticalmass.cm` to `127.0.0.1`
* additionally you might want to create tls certificates for your hostnames
* create the needed webserver configuration