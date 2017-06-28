<?php

namespace PhpCommon\Serializer\Format\Json\Resolver;

use PhpCommon\Serializer\Format\Json\JsonTypeSerializer;
use PhpCommon\Serializer\Format\Json\Registry\JsonTypeSerializerRegistry;
use PhpCommon\Serializer\Resolver\TypeHandlerResolver;

class JsonTypeSerializerResolver extends TypeHandlerResolver
{
    public function __construct(JsonTypeSerializerRegistry $locator)
    {
        parent::__construct($locator);
    }

    public function resolve($subject) : JsonTypeSerializer
    {
        return parent::resolve($subject);
    }
}