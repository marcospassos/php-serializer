<?php

namespace PhpCommon\Serializer\Exclusion;

use PhpCommon\Serializer\Context;
use PhpCommon\Serializer\ExclusionStrategy;

class DisjunctionExclusionStrategy implements ExclusionStrategy
{
    private $strategies;

    public function __construct(ExclusionStrategy ...$strategies)
    {
        $this->strategies = $strategies;
    }

    public function addStrategy(ExclusionStrategy $strategy) : void
    {
        $this->strategies[] = $strategy;
    }

    public function shouldSkipClass(string $class, Context $context) : bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldSkipClass($class, $context)) {
                return true;
            }
        }

        return false;
    }

    public function shouldSkipProperty(string $name, string $class, Context $context) : bool
    {
        foreach ($this->strategies as $strategy) {
            if ($strategy->shouldSkipProperty($name, $class, $context)) {
                return true;
            }
        }

        return false;
    }
}