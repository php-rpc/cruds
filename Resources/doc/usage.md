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
      mount: my_cruds_mount # must match one of routed resource (if you wish to use symfony routing) to be loaded
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

