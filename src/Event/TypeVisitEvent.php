<?php

namespace PhpCommon\Serializer\Event;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\Metadata\Type;

abstract class TypeVisitEvent extends DataEvent
{
    public function __construct(Context $context, $data, Type $type)
    {
        parent::__construct($context, $data, $type);
    }

    public function getType() : Type
    {
        return $this->type;
    }


}