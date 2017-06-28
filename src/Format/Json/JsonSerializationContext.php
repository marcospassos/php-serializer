<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\EventDispatcher;
use PhpCommon\Serializer\ExclusionStrategy;
use PhpCommon\Serializer\Metadata\Type;
use PhpCommon\Serializer\Format\Json\Resolver\JsonTypeSerializerResolver;

class JsonSerializationContext extends Context
{
    private $resolver;

    public function __construct(
        JsonTypeSerializerResolver $resolver,
        EventDispatcher $dispatcher,
        ExclusionStrategy $exclusionStrategy = null,
        array $properties = []
    ) {
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
        return Context::SERIALIZATION;
    }

    public function serialize($data, Type $type = null)
    {
        if ($type === null && $data === null) {
            return null;
        }

        if ($type === null || is_object($data)) {
            $type = Type::detect($data);
        }

        if ($type->isScalar()) {
            return $data;
        }

        $serializer = $this->resolver->resolve($type->getName());

        $callback = function ($data, Type $type) use ($serializer) {
            return $serializer->serialize($data, $type, $this);
        };

        return $this->accept($data, $type, $callback);
    }
}