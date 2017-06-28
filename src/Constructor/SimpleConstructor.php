<?php

namespace PhpCommon\Serializer\Constructor;

use PhpCommon\Serializer\Constructor;
use PhpCommon\Serializer\Metadata\Type;

class SimpleConstructor implements Constructor
{
    public function create(Type $type)
    {
        $name = $type->getName();

        return new $name;
    }
}