<?php

namespace PhpCommon\Serializer\Metadata;

interface MetadataFactory
{
    public function getMetadata(string $class) : ClassMetadata;
}