<?php

namespace PhpCommon\Serializer;

interface EventDispatcher
{
    public function dispatch(string $name, Event $event) : void;
}