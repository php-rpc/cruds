<?php

namespace ScayTrase\Api\Cruds\Event;

final class CrudEvents
{
    const READ   = 'read';
    const CREATE = 'create';
    const DELETE = 'delete';

    const PRE_UPDATE  = 'pre_update';
    const POST_UPDATE = 'post_update';
}
