<?php

namespace ScayTrase\Api\Cruds\Event;

final class CrudEvents
{
    const READ   = 'read';
    const CREATE = 'create';

    const PRE_UPDATE = 'pre_update';
    const PRE_DELETE = 'pre_delete';

    const POST_UPDATE = 'post_update';
    const POST_DELETE = 'post_delete';
}
