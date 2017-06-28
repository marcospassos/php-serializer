<?php

namespace PhpCommon\Serializer\Constructor;

use PhpCommon\Serializer\Constructor;
use PhpCommon\Serializer\Metadata\Type;

class ReflectionConstructor implements Constructor
{
    public function create(Type $type)
    {
        $reflection = new \ReflectionClass($type->getName());
        $constructor = $reflection->getConstructor();

        if ($constructor->getNumberOfRequiredParameters() === 0) {
            return $reflection->newInstance();
        }

        return $reflection->newInstanceWithoutConstructor();
    }
}