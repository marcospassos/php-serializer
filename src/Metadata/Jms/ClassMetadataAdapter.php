<?php

namespace PhpCommon\Serializer\Metadata\Jms;

use PhpCommon\Serializer\Metadata\ClassMetadata;
use PhpCommon\Serializer\Metadata\PropertyMetadata;
use PhpCommon\Serializer\Metadata\Type;
use ReflectionClass;
use Iterator;

class ClassMetadataAdapter implements ClassMetadata
{
    /**
     * @var JmsClassMetadata
     */
    private $metadata;

    public function __construct(JmsClassMetadata $source)
    {
        $this->metadata = $source;
    }

    public function getName(): string
    {
        return $this->metadata->name;
    }

    public function getType(): Type
    {
        return new Type($this->getName());
    }

    public function getDiscriminatorValue() : ?string
    {
        return $this->metadata->discriminatorValue;
    }

    public function getDiscriminatorMap() : array
    {
        return $this->metadata->discriminatorMap;
    }

    public function getDiscriminatorBaseClass() : ?string
    {
        return $this->metadata->discriminatorBaseClass;
    }

    public function getDiscriminatorPropertyName() : ?string
    {
        return $this->metadata->discriminatorPropertyName;
    }

    public function getDiscriminatorProperty() : ?string
    {
        return $this->metadata->discriminatorPropertyName;
    }

    public function getDiscriminatorClass(string $value) : string
    {
        if (!isset($this->metadata->discriminatorMap[$value])) {
            throw new \RuntimeException('Invalid discriminator value');
        }

        return $this->metadata->discriminatorMap[$value];
    }

    public function getReflection() : ReflectionClass
    {
        return $this->metadata->getReflection();
    }

    public function getProperty(string $name) : PropertyMetadata
    {
        /** @var JmsPropertyMetadata[] $properties */
        $properties = $this->metadata->propertyMetadata;

        if (!isset($properties[$name])) {
            throw new \InvalidArgumentException('Property ' . $name .' does not exist');
        }

        return $properties[$name]->getMetadata();
    }

    public function hasProperty(string $name): bool
    {
        return isset($this->metadata->propertyMetadata[$name]);
    }

    public function getProperties(): Iterator
    {
        foreach ($this->metadata->propertyMetadata as $name => $property) {
            yield $this->getProperty($name);
        }
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
}