# Installation

## Composer

Install project into your symfony application via composer command

```sh
composer require php-rpc/cruds:~1.0
```

## Kernel

Register `CrudsBundle` into your application kernel
 
```php

use ScayTrase\Api\Cruds\CrudsBundle;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return 
        [
        //...
            new CrudsBundle(),
        //...
        ];
    }
}
```
