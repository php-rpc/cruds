# Configuration

## Full configuration reference

```yaml
cruds:
    entities:

        # Prototype
        name:
            enabled:              true

            # Doctrine class
            class:                ~ # Required, Example: MyBundle:MyEntity

            # Route prefix. Defaults to entity key if not set
            prefix:               null # Example: /my-entity

            # Route mount. You can create different entries with different mounts. You can use this value when loading routes
            mount:                api # Example: my-mount-name

            # Entity repository. service reference, default to factory-acquired doctrine repository
            repository:           null # Example: @my_entity.repository

            # Action configuration
            actions:
                create:

                    # Service ID implementing 
                    # ScayTrase\Api\Cruds\EntityFactoryInterface
                    # Defaults to ScayTrase\Api\Cruds\ReflectionConstructorFactory
                    factory:              null # Example: @my_entity.factory

                    # Service ID implementing 
                    # ScayTrase\Api\Cruds\EntityFactoryInterface
                    # Defaults to ScayTrase\Api\Cruds\ReflectionConstructorFactory
                    processor:            null # Example: @my_entity.factory
                    enabled:              false

                    # Action path (will be prefixed with entity prefix)
                    path:                 /create
                read:
                    enabled:              false

                    # Action path (will be prefixed with entity prefix)
                    path:                 /get
                update:

                    # Service ID implementing 
                    # ScayTrase\Api\Cruds\EntityProcessorInterface
                    # Defaults to ScayTrase\Api\Cruds\PropertyAccessProcessor
                    processor:            null # Example: @my_entity.processor
                    enabled:              false

                    # Action path (will be prefixed with entity prefix)
                    path:                 /update
                delete:
                    enabled:              false

                    # Action path (will be prefixed with entity prefix)
                    path:                 /delete
                search:
                    enabled:              false

                    # Action path (will be prefixed with entity prefix)
                    path:                 /search

                    # Criteria modifiers. Array will be treated as nested criteria, allowing configuring several modifiers by key:value
                    criteria:             cruds.criteria.entity # Example: my.criteria.modifier
    listeners:

        # Configure param converting for symfony request
        param_converter:      true

        # Configure normalizer view event listener
        normalizer:           true

        # Configure serializer view event listener
        serializer:           true
```
