![drSoft.fr](logo.png)

# drSoft.fr Validate Customer Pro

## Table of contents

- [Presentation](#Presentation)
- [Requirements](#Requirements)
- [Install](#Install)
- [Links](#Links)
- [Authors](#Authors)
- [Licenses](#Licenses)

## Presentation

The drSoft.fr Validate Customer Pro module allows administrators to approve new user registrations, while also providing email notifications and automatic user group assignment.

## Requirements

This module requires PrestaShop 1.7.8 to work correctly.

This library also requires :

for production :

- [composer](https://getcomposer.org/)

for development :

- [composer](https://getcomposer.org/)

## Install

```bash
$ cd {PRESTASHOP_FOLDER}/modules
$ git clone git@github.com:drsoft-fr/prestashop-module-drsoftfrvalidatecustomerpro.git
$ mv prestashop-module-drsoftfrvalidatecustomerpro drsoftfrvalidatecustomerpro
$ cd drsoftfrvalidatecustomerpro
$ composer install -o --no-dev
$ cd {PRESTASHOP_FOLDER}
$ php ./bin/console prestashop:module install drsoftfrvalidatecustomerpro
$ php ./bin/console cache:clear --env=prod --no-debug
```

## Links

- [drSoft.fr on GitHub](https://github.com/drsoft-fr)
- [GitHub](https://github.com/drsoft-fr/prestashop-module-drsoftfrvalidatecustomerpro)
- [Issues](https://github.com/drsoft-fr/prestashop-module-drsoftfrvalidatecustomerpro/issues)

## Authors

**Dylan Ramos** - [on GitHub](https://github.com/dylan-ramos)

## Licenses

see LICENSE file
