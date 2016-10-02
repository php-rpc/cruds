# PHP Cruds bundle

[![License](https://poser.pugx.org/php-rpc/cruds/license)](https://packagist.org/packages/php-rpc/cruds)
[![Build Status](https://travis-ci.org/php-rpc/cruds.svg?branch=master)](https://travis-ci.org/php-rpc/cruds)
[![Code Coverage](https://scrutinizer-ci.com/g/php-rpc/cruds/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/php-rpc/cruds/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/php-rpc/cruds/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/php-rpc/cruds/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/php-rpc/cruds/version)](https://packagist.org/packages/php-rpc/cruds)
[![Latest Unstable Version](https://poser.pugx.org/php-rpc/cruds/v/unstable)](//packagist.org/packages/php-rpc/cruds)
[![Total Downloads](https://poser.pugx.org/php-rpc/cruds/downloads)](https://packagist.org/packages/php-rpc/cruds)

## Features

The main purpose of this library it to create easy configurable and extensible
API for entities:

 * Read controller with flexible entity querying and filtering
 * Create and update controllers with easy entity lifecycle control
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

## Usage

See [usage manual](Resources/doc/usage.md)

## Hooking (Event system)

See [usage manual](Resources/doc/events.md)

## Dependencies

Currently the main dependency is `doctrine/common` library which provides the 
`Criteria` and the `Selectable` interface as powerful tool to configure fetch the entities

## Integrations

See [integration reference](Resources/doc/integrations.md)

## Current limitations

### Property mapper

Currently only application-wide API property mapper is supported. This 
happens because you can use the relations during the criteria configuration
and the mapper used for the one entity should handle all of them.

This means that you cannot use both `jms/serializer` and `symfony/serializer` as
api metadata provider at the same time, you have to choose and convert mappings.
