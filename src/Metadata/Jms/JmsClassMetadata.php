<?php

namespace PhpCommon\Serializer\Metadata\Jms;

use Metadata\MergeableClassMetadata;
use Metadata\MergeableInterface;
use Psr\Log\InvalidArgumentException;
use ReflectionClass;
use LogicException;

class JmsClassMetadata extends MergeableClassMetadata
{
    public $discriminatorMap = [];
    public $discriminatorPropertyName;
    public $discriminatorBaseClass;
    public $discriminatorValue;
    public $attributes = [];

    private $metadata;

    public function __construct($name)
    {
        $this->name = $name;
        $this->createdAt = time();
    }

    public function getReflection() : ReflectionClass
    {
        if (!$this->reflection) {
            $this->reflection = new ReflectionClass($this->name);
        }

        return $this->reflection;
    }

    public function addDiscriminator(string $type, string $class) : void
    {
        $this->discriminatorMap[$type] = $class;
    }

    public function getMetadata()
    {
        if (!isset($this->metadata)) {
            $this->metadata = new ClassMetadataAdapter($this);
        }

        return $this->metadata;
    }

    public function merge(MergeableInterface $object)
    {
        if (!$object instanceof JmsClassMetadata) {
            throw new InvalidArgumentException(
                '$object must be an instance of JmsClassMetadata.'
            );
        }

        parent::merge($object);

        if ($object->discriminatorPropertyName && $this->discriminatorPropertyName) {
            throw new \LogicException(sprintf(
                'The discriminator of class "%s" would overwrite the ' .
                'discriminator of the parent class "%s". Please define all ' .
                'possible sub-classes in the discriminator of %s.',
                $object->name,
                $this->discriminatorBaseClass,
                $this->discriminatorBaseClass
            ));
        } elseif (!$this->discriminatorPropertyName && $object->discriminatorPropertyName) {
            $this->discriminatorPropertyName = $object->discriminatorPropertyName;
            $this->discriminatorMap = $object->discriminatorMap;
        }

        if ($object->discriminatorMap) {
            $this->discriminatorPropertyName = $object->discriminatorPropertyName;
            $this->discriminatorMap = $object->discriminatorMap;
            $this->discriminatorBaseClass = $object->discriminatorBaseClass;
        }

        if ($this->discriminatorMap) {
            $discriminatorValue = array_search(
                $this->name,
                $this->discriminatorMap,
                true
            );

            $this->discriminatorValue = $discriminatorValue ?: null;

            if ($this->discriminatorValue === null) {
                $reflection = $this->getReflection();

                if (!$reflection->isAbstract()) {
                    throw new LogicException(sprintf(
                        'The sub-class "%s" is not listed in the '.
                        'discriminator of the base class "%s".',
                        $this->name,
                        $this->discriminatorBaseClass
                    ));
                }
            }

            $propertyName = $this->discriminatorPropertyName;

            if (isset($this->propertyMetadata[$propertyName])) {
                throw new LogicException(sprintf(
                    'The discriminator field name "%s" of the base-class ' .
                    '"%s" conflicts with a regular property of the '.
                    'sub-class "%s".',
                    $propertyName,
                    $this->discriminatorBaseClass,
                    $this->name
                ));
            }
        }

        $this->attributes = array_replace(
            $this->attributes,
            $object->attributes
        );
    }


    public function serialize()
    {
        return serialize([
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->discriminatorMap,
            $this->discriminatorPropertyName,
            $this->discriminatorBaseClass,
            $this->discriminatorValue,
            $this->attributes,
            $this->createdAt,
        ]);
    }

    public function unserialize($str)
    {
        list(
            $this->name,
            $this->methodMetadata,
            $this->propertyMetadata,
            $this->fileResources,
            $this->discriminatorMap,
            $this->discriminatorPropertyName,
            $this->discriminatorBaseClass,
            $this->discriminatorValue,
            $this->attributes,
            $this->createdAt
            ) = unserialize($str);
    }
}