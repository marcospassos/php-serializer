<?php

namespace PhpCommon\Serializer\Metadata\Jms;

use PhpCommon\Serializer\Metadata\PropertyMetadata;
use PhpCommon\Serializer\Metadata\Type;
use ReflectionMethod;
use ReflectionProperty;

class PropertyMetadataAdapter implements PropertyMetadata
{
    /**
     * @var JmsPropertyMetadata
     */
    public $metadata;

    /**
     * @var ReflectionMethod|ReflectionProperty
     */
    public $readAccessor;

    /**
     * @var ReflectionMethod|ReflectionProperty
     */
    public $writeAccessor;

    public function __construct(JmsPropertyMetadata $source)
    {
        $this->metadata = $source;
    }

    public function getClass(): string
    {
        return $this->metadata->class;
    }

    public function getName(): string
    {
        return $this->metadata->name;
    }

    public function isVirtual(): bool
    {
        return $this->metadata->virtual;
    }

    public function getType(): Type
    {
        return $this->metadata->type;
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->metadata->attributes);
    }

    public function getAttribute(string $name, $default = null)
    {
        return $this->metadata->attributes[$name] ?? $default;
    }

    public function getAttributes() : array
    {
        return $this->metadata->attributes;
    }

    public function getValue($object)
    {
        $accessor = $this->getReadAccessor();

        if ($accessor instanceof ReflectionProperty) {
            return $accessor->getValue($object);
        }

        return $accessor->invoke($object);
    }

    public function setValue($object, $value)
    {
        $accessor = $this->getWriteAccessor();

        if ($accessor instanceof ReflectionProperty) {
            $accessor->setValue($object, $value);

            return;
        }

        $accessor->invoke($object, $value);
    }

    /**
     * @return ReflectionMethod|ReflectionProperty
     */
    private function getReadAccessor()
    {
        if ($this->readAccessor) {
            return $this->readAccessor;
        }

        if ($this->metadata->getter) {
            $this->readAccessor = new ReflectionMethod(
                $this->metadata->class,
                $this->metadata->getter
            );
            $this->readAccessor->setAccessible(true);
        } else {
            $this->readAccessor = $this->metadata->getReflection();
            $this->readAccessor->setAccessible(true);
        }

        return $this->readAccessor;
    }

    /**
     * @return ReflectionMethod|ReflectionProperty
     */
    private function getWriteAccessor()
    {
        if ($this->writeAccessor) {
            return $this->writeAccessor;
        }

        if ($this->metadata->setter) {
            $this->writeAccessor = new ReflectionMethod(
                $this->metadata->class,
                $this->metadata->setter
            );
            $this->writeAccessor->setAccessible(true);
        } else {
            $this->writeAccessor = $this->metadata->getReflection();
            $this->writeAccessor->setAccessible(true);
        }

        return $this->writeAccessor;
    }
}