<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\ContextBuilder;
use PhpCommon\Serializer\EventDispatcher;
use PhpCommon\Serializer\Format\Json\Registry\JsonTypeDeserializerRegistry;
use PhpCommon\Serializer\Format\Json\Resolver\JsonTypeDeserializerResolver;
use PhpCommon\Serializer\ServiceProvider;
use RuntimeException;

class JsonDeserializationContextBuilder extends ContextBuilder
{
    private $defaultDeserializers;
    private $dispatcher;

    public function __construct(ServiceProvider $provider, EventDispatcher $dispatcher, array $defaultDeserializers, array $properties = [])
    {
        parent::__construct($provider, $properties);

        $this->dispatcher = $dispatcher;
        $this->defaultDeserializers = $defaultDeserializers;
    }

    public function create() : JsonDeserializationContext
    {
        $deserializers = $this->loadDeserializers();
        $registry = new JsonTypeDeserializerRegistry($deserializers);
        $resolver = new JsonTypeDeserializerResolver($registry);
        $exclusionStrategy = $this->getExclusionStrategy();
        $properties = $this->getProperties();
        
        return new JsonDeserializationContext(
            $resolver,
            $this->dispatcher,
            $exclusionStrategy,
            $properties
        );
    }

    protected function getDeserializers(): array
    {
        return array_merge(
            $this->defaultDeserializers,
            parent::getDeserializers()
        );
    }

    private function loadDeserializers() : array
    {
        $deserializers = [];

        foreach ($this->getDeserializers() as $type => $deserializer) {
            if (is_string($deserializer)) {
                $deserializer = $this->provider->getService($deserializer);
            }

            if (!($deserializer instanceof JsonTypeDeserializer)) {
                throw new RuntimeException(sprintf(
                    'The deserializer for "%s" must be a ' .
                    'JsonTypeDeserializer, but found %s.',
                    $type,
                    is_object($deserializer) ?
                        get_class($deserializer) :
                        gettype($deserializer)
                ));
            }

            $deserializers[$type] = $deserializer;
        }

        return $deserializers;
    }
}