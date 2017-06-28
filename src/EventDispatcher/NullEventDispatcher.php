<?php

namespace PhpCommon\Serializer\EventDispatcher;

use PhpCommon\Serializer\Event;
use PhpCommon\Serializer\EventDispatcher;

class NullEventDispatcher implements EventDispatcher
{
    public function dispatch(string $name, Event $event) : void
    {
        // Do nothing
    }
}