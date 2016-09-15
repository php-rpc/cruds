# Usage examples

## Routing

If you are using default symfony routing processing, you can enable your mount point
as resource. Options are inherited

```yaml
cruds:
  resource: my_cruds_mount
  prefix: /api/entity
  type: cruds_mount
  options:
    context:
      # groups: ['Default', 'default']
```

You can also process routes manually with accessing `@cruds.api.router_loader` service
to obtain list of mounts and mount routes.


## Bootstrap your CRUD API

Easy configuration with loaded routing will produce five actions immediately after 
entity configuration:

```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount # must match one of routed resource (if you wish to use symfony routing) to be loaded
      actions: [create, read, update, delete, search]
```

Having only this you can create, read, update and delete instances of `MyBundle:MyEntity` via REST API calls.

## Search criteria configurator

Out of the box the library provides you the simple way to configure search filtering - entity criteria. 
You can pass `scalar|array|null` as field:value array to produce `where` clauses `=|IN|IS NULL` respectively
 
You can replace this behavior to match your business logic in one easy way - implement your own
`CriteriaConfiguratorInterface` service. The service should accept the `Criteria` class and the criteria values
from the action arguments and the class the criteria is configured for.

As a built-in extension the library provides `NestedCriteriaConfigurator`, allowing you to split criteria options
to pass them to several independent services, i.e pass `criteria[entity]` arguments to `EntityCriteriaConfiguration`
and `criteria[my-logic]` to your own implementation.

`NestedCriteriaConfigurator` is configured automatically if you provide the service ids to `search` action configuration:

```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount 
      actions: 
        create: ~
        read: ~
        update: ~
        delete: ~
        search:
          criteria:
            entity: cruds.criteria.entity
            my-logic: my_app.cruds.my_logic_criteria
```

## Entity create/update processing

### Entity factory

As a part of your business logic you can create entities with `create` action.
But as soon your entities are black box for the library you have an options to
define your own entity factory, which instantiate entities in your special way
(i.e. with constructor arguments) before the library populate this entity with
data from the action arguments

`EntityFactoryInterface` defines the simple interface which accepts all the
data received by `create` action and return the new objects. As a bootstrap value
library provide `ReflectionConstructorFactory` which can be extended to
use data as constructor arguments in addition to some defaults.
 
You can set up your factory with `create` action configuration passing it's
service id to `factory` option:

```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount 
      actions: 
        create:
          factory: my_app.cruds.entity_factory
        read: ~
        update: ~
        delete: ~
        search: ~
```

When the entity is being updated the factory is not used since `ObjectRepository`
provides the controller with ready to use entity (basically from database using `doctrine/orm` library) 

### Entity processor

The next step in creating or updating your entity is to patch it with some data.
Here the entity processor takes the action.

`EntityProcessorInterface` define the simple interface that takes the entity, incoming data
and outputs patched data. Out of the box implementation utilize the `symfony/property-access`
component to call accessor methods on the entity to set the data. The methods name 
are searched according the data keys.

As the part of adopting the `doctrine/orm` library the `PropertyAccessor` is decorated
with doctrine registry to automatically convert incoming relation identifiers into object 
references.

You can implement your own processor or configure some built-in ones (like 
`FormProcessor` from symfony adapters).

Create and update processors are configured separately, allowing you to
define different handling when creating and updating entities

```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount 
      actions: 
        create:
          processor: my_app.cruds.entity_processor
        read: ~
        update:
          processor: my_app.cruds.entity_processor
        delete: ~
        search: ~
```

## Controlling the paths

This library allows you to update paths for the action. The path are hierarchical
so you cannot put `read` method path outside the configured entity or mount prefix, so
you have to make the prefix to be empty and do full path configuration at the method node

Also you can use route placeholders to benefit from routing matching having 
substituting action arguments with placeholder data 

```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount 
      actions: 
        read:
          path: /{identifier}/get
```
