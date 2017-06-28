<?php

namespace PhpCommon\Serializer\Format\Json\Resolver;

use PhpCommon\Serializer\Format\Json\JsonTypeDeserializer;
use PhpCommon\Serializer\Format\Json\Registry\JsonTypeDeserializerRegistry;
use PhpCommon\Serializer\Resolver\TypeHandlerResolver;

class JsonTypeDeserializerResolver extends TypeHandlerResolver
{
    public function __construct(JsonTypeDeserializerRegistry $locator)
    {
        parent::__construct($locator);
    }

    public function resolve($subject) : JsonTypeDeserializer
    {
        return parent::resolve($subject);
    }
}