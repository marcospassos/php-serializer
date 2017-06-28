<?php

namespace PhpCommon\Serializer\Event;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\Event;
use PhpCommon\Serializer\Metadata\Type;

class DataEvent extends Event
{
    protected $data;
    protected $type;

    public function __construct(Context $context, $data, Type $type = null)
    {
        parent::__construct($context);

        $this->data = $data;
        $this->type = $type;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getType() : ?Type
    {
        return $this->type;
    }
}