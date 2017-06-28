<?php

namespace PhpCommon\Serializer\Exclusion;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\ExclusionStrategy;

class NoExclusionStrategy implements ExclusionStrategy
{
    public function shouldSkipClass(string $class, Context $context) : bool
    {
        return false;
    }

    public function shouldSkipProperty(string $name, string $class, Context $context) : bool
    {
        return false;
    }
}