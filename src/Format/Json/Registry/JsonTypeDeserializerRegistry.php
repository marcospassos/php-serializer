<?php

namespace PhpCommon\Serializer\Format\Json\Registry;

use PhpCommon\ResourceResolver\Locator\ArrayRegistry;
use PhpCommon\Serializer\Format\Json\JsonTypeDeserializer;
use InvalidArgumentException;

class JsonTypeDeserializerRegistry extends ArrayRegistry
{
    public function __construct(array $deserializers = [])
    {
        foreach ($deserializers as $deserializer) {
            if ($deserializer instanceof JsonTypeDeserializer) {
                continue;
            }

            throw new InvalidArgumentException(sprintf(
                'Expected instance of %s, but got %s.',
                JsonTypeDeserializer::class,
                is_object($deserializer) ?
                    get_class($deserializer) :
                    gettype($deserializer)
            ));
        }

        parent::__construct($deserializers);
    }

    public function get($name) : JsonTypeDeserializer
    {
        return parent::get($name);
    }
}