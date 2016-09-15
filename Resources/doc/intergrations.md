# Integrations

You can provide entity lifecycle configurations using several external 
library. Already integrated are the `symfony` itself, `doctrine/orm` and `jms/serializer`.

They can automatically benefit from other installed libraries, i.e `symfony/form` could
immediately use `symfony/validator` if configured. Below are some integrated libraries
and feature the can provide if enabled

## `doctrine/orm`

Doctrine ORM provides us the basic mapping information, so you can translate
your entities into API representation using 1:1 doctrine entity mapping
 
Provides:

* Converting the identifiers to relation objects when setting the association properties
* Handling the bundle notation `MyBundle:MyEntity` for configuration
* Serializing entity relations into identifier on entity read
* Property mapping with doctrine mappings

## `symfony/form`

Symfony forms provide a way to convert you API source data into the final 
entities using the well known form classes

Provides:
* `FormProcessor` - use any suitable `FormTypeInterface` as the entity processor
* `AutoFormProcessor` - generates form processor on the fly

## `symfony/serializer`

Symfony serializer provides the configuration of the 'appearance' of you entities
in the view layer. You can use this tool to convert your entities into JSON\XML or
the any notation you like.

Provides:
* Entity read serialization
* Denormalization processor
* Property mapping with serializer metadata 

## `jms/serializer`

Mostly as above but uses another source of representation configuration.

Provides:
* Entity read serialization
* Denormalization processor
* Property mapping with serializer metadata
