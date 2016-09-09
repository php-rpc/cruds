# PHP Cruds bundle [WIP]

[![License](https://poser.pugx.org/php-rpc/cruds/license)](https://packagist.org/packages/php-rpc/cruds)
[![Build Status](https://travis-ci.org/php-rpc/cruds.svg?branch=master)](https://travis-ci.org/php-rpc/cruds)
[![Code Coverage](https://scrutinizer-ci.com/g/php-rpc/cruds/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-rpc/cruds/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/php-rpc/cruds/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/php-rpc/cruds/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/php-rpc/cruds/cruds)](https://packagist.org/packages/php-rpc/cruds)
[![Latest Unstable Version](https://poser.pugx.org/php-rpc/cruds/v/unstable)](//packagist.org/packages/php-rpc/cruds)
[![Total Downloads](https://poser.pugx.org/php-rpc/cruds/downloads)](https://packagist.org/packages/php-rpc/cruds)

## Goal

The main purpose of this library it to create easy configurarle and extensible
library for basic CRUD purposes.

 * Read controller with flexible entity querying and filtering
 * Create and update controllers with easy entity lifecycle controlling
 * Basic access checks

## Installation 

1. Require package from packagist:

```sh
composer require php-rpc/cruds:~1.0
```

2. Include bundle into your application kernel

```php
class AppKernel extends Kernel {
    public function registerBundles()
    {
        return [
            //...
            new ScayTrase\Api\Cruds\CrudsBundle(),
            //...
        ];
    }
}
```

That's all, you are ready to go!

## Configuration

See [Configuration reference](Resources/doc/configuration.md)

## Dependencies

Currently the main dependency is `doctrine/orm` library which provides the 
`QueryBuilder` as powerful tool to configure the entity filters

- [ ] Todo: try do lower the dependency to `doctrine/collections` or something like this 

## Integrations

You can provide entity lifecycle configurations using several external 
library. Already integrated are the `symfony` itself, `doctrine/orm` and `jms/serializer`.

They can automatically benefit from other installed libraries, i.e `symfony/form` could
immediately use `symfony/validator` if configured

### `doctrine/orm`

Doctrine ORM provides us the basic mapping information, so you can translate
your entities into API representation using 1:1 doctrine entity mapping 

Also doctrine provides the most important part of 

### `symfony/form`

Symfony forms provide a way to convert you API source data into the final 
entities using the well known form classes

### `symfony/serializer`

Symfony serializer provides the configuration of the 'appearance' of you entities
in the view layer. You can use this tool to convert your entities into JSON\XML or
the any notation you like.

### `jms/serializer`

Mostly as above but uses another source of representation configuration.


## Current limitations

### Property mapper

Currently only application-wide API property mapper is supported. This 
happens because you can use the relations during the criteria configuration
and the mapper used for the one entity should handle all of them.

This means that you cannot use both `jms/serializer` and `symfony/serializer` as
api metadata provider at the same time, you have to choose and convert mappings.
