# Event system

All CRUDS events a inhereted from `CollectionCrudEvent` which contains the collection
of entities being processed (read or write). You can hook this events to modify your 
business logic.

Library uses built-in symfony event dispatcher.

## Access events

* `read` event occurs on read and search requests.
* `create` event occurs after entity is created.
* `pre_update` event occurs after entity was fetched to update
* `post_update` event occurs after entity was updated
* `delete` event occurs before entity was deleted
