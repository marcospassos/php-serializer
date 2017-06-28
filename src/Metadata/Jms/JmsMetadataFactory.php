<?php

namespace PhpCommon\Serializer\Metadata\Jms;

use PhpCommon\Serializer\Metadata\ClassMetadata;
use PhpCommon\Serializer\Metadata\MetadataFactory;
use Metadata\MetadataFactory as AdaptedFactory;

class JmsMetadataFactory implements MetadataFactory
{
    private $factory;

    public function __construct(AdaptedFactory $factory)
    {
        $this->factory = $factory;
    }

    public function getMetadata(string $class): ClassMetadata
    {
        $metadata = $this->factory->getMetadataForClass($class);

        if (!($metadata instanceof JmsClassMetadata)) {
            throw new \RuntimeException('Incompatible metadata factory.');
        }

        return $metadata->getMetadata();
    }
}