<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\EventDispatcher;
use PhpCommon\Serializer\ExclusionStrategy;
use PhpCommon\Serializer\Metadata\Type;
use PhpCommon\Serializer\Format\Json\Resolver\JsonTypeDeserializerResolver;

class JsonDeserializationContext extends Context
{
    private $resolver;

    public function __construct(JsonTypeDeserializerResolver $resolver, EventDispatcher $dispatcher, ExclusionStrategy $exclusionStrategy = null, array $properties = [])
    {
        parent::__construct($dispatcher, $exclusionStrategy, $properties);

        $this->resolver = $resolver;
        $this->dispatcher = $dispatcher;
    }

    public function getFormat(): string
    {
        return 'json';
    }

    public function getDirection() : string
    {
        return Context::DESERIALIZATION;
    }

    public function deserialize($data, Type $type)
    {
        if ($type->isScalar()) {
            return $data;
        }

        $deserializer = $this->resolver->resolve($type->getName());

        $callback = function ($data, Type $type) use ($deserializer) {
            return $deserializer->deserialize($data, $type, $this);
        };

        return $this->accept($data, $type, $callback);
    }
}