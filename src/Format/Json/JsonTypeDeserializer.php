<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\Metadata\Type;

interface JsonTypeDeserializer
{
    public function deserialize($data, Type $type, JsonDeserializationContext $context);
}