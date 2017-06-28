<?php

namespace PhpCommon\Serializer\Metadata\Jms;

use PhpCommon\Serializer\Metadata\Type;
use Metadata\PropertyMetadata as BasePropertyMetadata;
use ReflectionProperty;

class JmsPropertyMetadata extends BasePropertyMetadata
{
    /**
     * @var string
     */
    public $getter;

    /**
     * @var string
     */
    public $setter;

    /**
     * @var Type
     */
    public $type;

    public $attributes = [];

    public $virtual = false;

    private $metadata;

    public function __construct(string $class, string $name)
    {
        $this->class = $class;
        $this->name = $name;
    }

    public function getReflection(): ReflectionProperty
    {
        if (!$this->reflection) {
            $this->reflection = new ReflectionProperty($this->class, $this->name);
        }

        return $this->reflection;
    }

    public function getMetadata()
    {
        if (!isset($this->metadata)) {
            $this->metadata = new PropertyMetadataAdapter($this);
        }

        return $this->metadata;
    }

    public function serialize()
    {
        return serialize([
            $this->class,
            $this->name,
            $this->type,
            $this->getter,
            $this->setter,
            $this->virtual,
            $this->attributes,
        ]);
    }

    public function unserialize($str)
    {
        list(
            $this->class,
            $this->name,
            $this->type,
            $this->getter,
            $this->setter,
            $this->virtual,
            $this->attributes,
            ) = unserialize($str);
    }
}