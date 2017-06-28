<?php

namespace PhpCommon\Serializer\Exclusion;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\ExclusionStrategy;

class PropertyExclusionStrategy implements ExclusionStrategy
{
    private $class;
    private $property;

    public function __construct(string $class, string $property)
    {
        $this->class = $class;
        $this->property = $property;
    }

    public function shouldSkipClass(string $class, Context $context) : bool
    {
        return false;
    }

    public function shouldSkipProperty(string $name, string $class, Context $context) : bool
    {
        return is_a($class, $this->class, true) && $this->property === $name;
    }
}