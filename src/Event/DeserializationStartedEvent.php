<?php

namespace PhpCommon\Serializer\Event;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\Metadata\Type;

class DeserializationStartedEvent extends DataEvent
{
    public function __construct(Context $context, string $data, Type $type)
    {
        parent::__construct($context, $data, $type);
    }

    public function getType() : Type
    {
        return parent::getType();
    }

    public function getData() : string
    {
        return parent::getData();
    }
}