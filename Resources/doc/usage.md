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

### Entity configuration
```yaml
cruds:
  entities:
    my-entity-0:
      class: MyBundle:MyEntity
      prefix: /my-entity
      mount: my_cruds_mount # must match one of routed resource (if you wish to use symfony routing) to be loaded
      actions: [create, read, update, delete, search]
```
