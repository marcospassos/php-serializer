<?php

namespace PhpCommon\Serializer\Format\Json\Registry;

use PhpCommon\ResourceResolver\Locator\ArrayRegistry;
use PhpCommon\Serializer\Format\Json\JsonTypeSerializer;
use InvalidArgumentException;

class JsonTypeSerializerRegistry extends ArrayRegistry
{
    public function __construct(array $serializers = [])
    {
        foreach ($serializers as $serializer) {
            if ($serializer instanceof JsonTypeSerializer) {
                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s, but got %s.',
                JsonTypeSerializer::class,
                is_object($serializers) ?
                    get_class($serializer) :
                    gettype($serializer)
            ));
        }

        parent::__construct($serializers);
    }

    public function get($name) : JsonTypeSerializer
    {
        return parent::get($name);
    }
}