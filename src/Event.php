<?php

namespace PhpCommon\Serializer;

class Event
{
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function getContext(): Context
    {
        return $this->context;
    }
}