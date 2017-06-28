<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\ContextBuilder;
use PhpCommon\Serializer\EventDispatcher;
use PhpCommon\Serializer\Format\Json\Registry\JsonTypeSerializerRegistry;
use PhpCommon\Serializer\Format\Json\Resolver\JsonTypeSerializerResolver;
use PhpCommon\Serializer\ServiceProvider;
use RuntimeException;

class JsonSerializationContextBuilder extends ContextBuilder
{
    private $dispatcher;
    private $defaultSerializers;

    public function __construct(ServiceProvider $provider, EventDispatcher $dispatcher, array $defaultSerializers = [], array $properties = [])
    {
        parent::__construct($provider, $properties);

        $this->dispatcher = $dispatcher;
        $this->defaultSerializers = $defaultSerializers;
    }

    public function create() : JsonSerializationContext
    {
        $serializers = $this->loadSerializers();
        $registry = new JsonTypeSerializerRegistry($serializers);
        $resolver = new JsonTypeSerializerResolver($registry);
        $exclusionStrategy = $this->getExclusionStrategy();
        $properties = $this->getProperties();

        return new JsonSerializationContext(
            $resolver,
            $this->dispatcher,
            $exclusionStrategy,
            $properties
        );
    }

    protected function getSerializers(): array
    {
        return array_merge($this->defaultSerializers, parent::getSerializers());
    }

    private function loadSerializers() : array
    {
        $serializers = [];

        foreach ($this->getSerializers() as $type => $serializer) {
            if (is_string($serializer)) {
                $serializer = $this->provider->getService($serializer);
            }

            if (!($serializer instanceof JsonTypeSerializer)) {
                throw new RuntimeException(sprintf(
                    'The serializer for "%s" must be a ' .
                    'JsonTypeSerializer, but found %s.',
                    $type,
                    is_object($serializer) ?
                        get_class($serializer) :
                        gettype($serializer)
                ));
            }

            $serializers[$type] = $serializer;
        }

        return $serializers;
    }
}