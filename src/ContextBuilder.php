<?php

namespace PhpCommon\Serializer;

use PhpCommon\Serializer\Exclusion\DisjunctionExclusionStrategy;
use RuntimeException;

abstract class ContextBuilder
{
    protected $provider;
    private $properties;
    private $serializers;
    private $deserializers;
    private $exclusionStrategies;

    public function __construct(ServiceProvider $provider, array $properties = [])
    {
        $this->provider = $provider;
        $this->properties = $properties;
    }

    public function registerHandler(string $type, $handler) : void
    {
        $this->registerSerializer($type, $handler);
        $this->registerDeserializer($type, $handler);
    }

    public function registerSerializers(array $serializers) : void
    {
        foreach ($serializers as $type => $serializer) {
            $this->registerSerializer($type, $serializer);
        }
    }

    public function registerDeserializers(array $deserializers) : void
    {
        foreach ($deserializers as $type => $deserializer) {
            $this->registerDeserializer($type, $deserializer);
        }
    }

    public function registerSerializer(string $type, $serializer) : void
    {
        $this->serializers[$type] = $serializer;
    }

    public function registerDeserializer(string $type, $deserializer) : void
    {
        $this->deserializers[$type] = $deserializer;
    }

    public function addExclusionStrategy($strategy) : void
    {
        $this->exclusionStrategies[] = $strategy;
    }

    public function setProperty(string $name, $value) : void
    {
        $this->properties[$name] = $value;
    }

    protected function getProperties() : array
    {
        return $this->properties;
    }

    protected function getSerializers() : array
    {
        return $this->serializers;
    }

    protected function getDeserializers() : array
    {
        return $this->deserializers;
    }

    protected function getExclusionStrategy() : ?ExclusionStrategy
    {
        $strategies = $this->loadExclusionStrategies();

        if (empty($strategies)) {
            return null;
        }

        if (count($strategies) === 1) {
            return reset($strategies);
        }

        return new DisjunctionExclusionStrategy(...$strategies);
    }

    private function loadExclusionStrategies() : array
    {
        $strategies = [];

        foreach ($this->exclusionStrategies as $strategy) {
            if (is_string($strategy)) {
                $strategy = $this->provider->getService($strategy);
            }

            if (!($strategy instanceof ExclusionStrategy)) {
                throw new RuntimeException(sprintf(
                    'The exclusion strategy must be of type %s, ' .
                    'but found %s.',
                    ExclusionStrategy::class,
                    is_object($strategy) ?
                        get_class($strategy) :
                        gettype($strategy)
                ));
            }

            $strategies[] = $strategy;
        }

        return $strategies;
    }
}