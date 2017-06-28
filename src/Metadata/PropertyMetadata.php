<?php

namespace PhpCommon\Serializer\Metadata;

interface PropertyMetadata
{
    public function getClass(): string;
    public function getName(): string;
    public function isVirtual() : bool;
    public function getType(): Type;
    public function getValue($object);
    public function setValue($object, $value);
    public function hasAttribute(string $name) : bool;
    public function getAttribute(string $name, $default = null);
    public function getAttributes();
}