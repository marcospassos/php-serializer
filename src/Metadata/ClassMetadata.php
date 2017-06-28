<?php

namespace PhpCommon\Serializer\Metadata;

use ReflectionClass;
use Iterator;

interface ClassMetadata
{
    public function getName(): string;
    public function getType(): Type;
    public function getDiscriminatorValue() : ?string;
    public function getDiscriminatorMap() : array;
    public function getDiscriminatorBaseClass() : ?string;
    public function getDiscriminatorPropertyName() : ?string;
    public function getDiscriminatorProperty() : ?string;
    public function getDiscriminatorClass(string $value) : string;
    public function getReflection() : ReflectionClass;
    public function getProperty(string $name) : PropertyMetadata;
    public function hasProperty(string $name) : bool;

    /**
     * @return Iterator|PropertyMetadata[]
     */
    public function getProperties() : Iterator;

    public function hasAttribute(string $name) : bool;
    public function getAttribute(string $name, $default = null);
    public function getAttributes();
}