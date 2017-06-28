<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\Metadata\Type;

interface JsonTypeSerializer
{
    public function serialize($data, Type $type, JsonSerializationContext $context);
}