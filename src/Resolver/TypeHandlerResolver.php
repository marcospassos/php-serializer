<?php

namespace PhpCommon\Serializer\Resolver;

use PhpCommon\ResourceResolver\ResolverStrategy;
use PhpCommon\ResourceResolver\ResourceRegistry;
use PhpCommon\ResourceResolver\ResourceResolver;
use PhpCommon\ResourceResolver\Strategy\CachedClassHierarchyStrategy;
use PhpCommon\ResourceResolver\Strategy\CachedStrategy;
use PhpCommon\ResourceResolver\Strategy\DelegateStrategy;
use PhpCommon\ResourceResolver\Strategy\NameStrategy;

class TypeHandlerResolver implements ResourceResolver
{
    private $strategy;
    private $registry;

    public function __construct(ResourceRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function setStrategy(ResolverStrategy $strategy)
    {
        $this->strategy = $strategy;
    }

    public function resolve($subject)
    {
        if ($this->strategy == null) {
            $this->strategy = $this->getDefaultStrategy();
        }

        return $this->strategy->resolve($subject, $this->registry);
    }

    protected function getDefaultStrategy() : ResolverStrategy
    {
        return new CachedStrategy(
            new DelegateStrategy(
                new NameStrategy(),
                new CachedClassHierarchyStrategy('object')
            ));
    }
}