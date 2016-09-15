# Integrations

You can provide entity lifecycle configurations using several external 
library. Already integrated are the `symfony` itself, `doctrine/orm` and `jms/serializer`.

They can automatically benefit from other installed libraries, i.e `symfony/form` could
immediately use `symfony/validator` if configured

## `doctrine/orm`

Doctrine ORM provides us the basic mapping information, so you can translate
your entities into API representation using 1:1 doctrine entity mapping 

## `symfony/form`

Symfony forms provide a way to convert you API source data into the final 
entities using the well known form classes

## `symfony/serializer`

Symfony serializer provides the configuration of the 'appearance' of you entities
in the view layer. You can use this tool to convert your entities into JSON\XML or
the any notation you like.

## `jms/serializer`

Mostly as above but uses another source of representation configuration.

