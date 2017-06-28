<?php

namespace PhpCommon\Serializer\Event;

use PhpCommon\Serializer\Metadata\Type;

class TypeEnteredEvent extends TypeVisitEvent
{
    public function setType(Type $type) : void
    {
        $this->type = $type;
    }
}