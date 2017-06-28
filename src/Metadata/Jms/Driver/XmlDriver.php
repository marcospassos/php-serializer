<?php

namespace PhpCommon\Serializer\Metadata\Jms\Driver;

use PhpCommon\Serializer\Metadata\Jms\JmsClassMetadata;
use PhpCommon\Serializer\Metadata\Jms\JmsPropertyMetadata;
use PhpCommon\Serializer\Metadata\Type;
use Metadata\Driver\AbstractFileDriver;
use RuntimeException;

class XmlDriver extends AbstractFileDriver
{
    protected function loadMetadataFromFile(\ReflectionClass $class, $path)
    {
        $previous = libxml_use_internal_errors(true);
        libxml_clear_errors();
        $elem = simplexml_load_file($path);
        libxml_use_internal_errors($previous);

        if (false === $elem) {
            throw new \RuntimeException(libxml_get_last_error());
        }

        $metadata = new JmsClassMetadata($name = $class->name);

        if (!$elems = $elem->xpath("./class[@name = '" . $name . "']")) {
            throw new RuntimeException(
                sprintf('Could not find class %s inside XML element.', $name)
            );
        }

        $elem = reset($elems);
        $metadata->fileResources[] = $path;
        $metadata->fileResources[] = $class->getFileName();

        $classAttributes = $elem->attributes();

        if (isset($classAttributes->{'discriminator-property'})) {
            $metadata->discriminatorPropertyName =
                (string) $elem->attributes()->{'discriminator-property'};
            $metadata->discriminatorBaseClass = $class->name;
        }

        foreach ($elem->xpath('./property') as $propertyElement) {
            $attributes = $propertyElement->attributes();

            if (!isset($attributes->name)) {
                throw new \RuntimeException('Name is required');
            }

            $propertyName = (string) $attributes->name;
            $declaringClass = $this->getDeclaringClass($propertyName, $class);
            $owningClass = $metadata->name;

            if ($declaringClass !== null) {
                $owningClass = $declaringClass->getName();
            }

            $propertyMetadata = new JmsPropertyMetadata(
                $owningClass,
                (string) $attributes->name
            );

            $propertyMetadata->virtual = $declaringClass === null;

            if (isset($attributes->type)) {
                $type = (string) $attributes->type;
            } else if (isset($propertyElement->type)) {
                $type = (string) $propertyElement->type;
            } else {
                throw new \RuntimeException('Type is required for '.$attributes->name . ' in ' . $class->name);
            }

            $propertyMetadata->type = Type::parse((string) $type);

            if (isset($attributes->getter)) {
                $propertyMetadata->getter = (string) $attributes->getter;
            }

            if (isset($attributes->setter)) {
                $propertyMetadata->setter = (string) $attributes->setter;
            }

            if (isset($propertyElement->attributes)) {
                $propertyMetadata->attributes = $this->loadAttributes(
                    $propertyElement->attributes
                );
            }

            $metadata->addPropertyMetadata($propertyMetadata);
        }

        foreach ($elem->xpath('./discriminator') as $discriminator) {
            $attributes = $discriminator->attributes();

            if (!isset($attributes->value)) {
                throw new RuntimeException(
                    'Each discriminator element must have a "value" attribute.'
                );
            }

            $metadata->addDiscriminator(
                (string) $attributes->value,
                (string) $discriminator
            );
        }

        if (isset($elem->attributes)) {
            $metadata->attributes = $this->loadAttributes(
                $elem->attributes
            );
        }

        return $metadata;
    }

    private function getDeclaringClass(string $property, \ReflectionClass $class)
    {
        do {
            if ($class->hasProperty($property)) {
                return $class;
            }
        } while ($class = $class->getParentClass());

        return null;
    }

    private function loadAttributes(\SimpleXMLElement $element)
    {
        $attributes = [];
        foreach ($element->xpath('./attribute') as $child) {
            $elementAttributes = $child->attributes();

            $value = (string) $child;
            $type = null;

            if (!isset($elementAttributes->type)) {
                $type = strtolower((string) $elementAttributes->type);
            }

            $lowercaseValue = strtolower($value);

            switch($type) {
                case 'number':
                    $value += 0;
                    break;

                case 'boolean':
                    $value = $lowercaseValue === 'true';
                    break;

                case 'null':
                    $value = null;
                    break;

                case 'collection':
                    $value = $this->loadAttributes($child);
                    break;

                default:
                    $value = $this->parseAttributeValue($value);
                    break;
            }

            if (isset($elementAttributes->name)) {
                $attributes[(string) $elementAttributes->name] = $value;
            } else {
                $attributes[] = $value;
            }
        }

        return $attributes;
    }

    private function parseAttributeValue(string $value)
    {
        $lowercaseValue = strtolower($value);

        switch(true) {
            case is_numeric($value):
                return $value + 0;

            case (in_array($lowercaseValue, ['true', 'false'])):
                return $lowercaseValue === 'true';

            case ($lowercaseValue === 'null'):
                return null;
        }

        return $value;
    }

    protected function getExtension()
    {
        return 'xml';
    }
}
