<?php

namespace PhpCommon\Serializer;

interface ExclusionStrategy
{
    public function shouldSkipClass(string $class, Context $context) : bool;
    public function shouldSkipProperty(string $name, string $class, Context $context) : bool;
}