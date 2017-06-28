<?php

namespace PhpCommon\Serializer;

class SerializerEvents
{
    public const SERIALIZATION_STARTED = 'serialization_starting';
    public const SERIALIZATION_FINISHED = 'serialization_finished';

    public const DESERIALIZATION_STARTING = 'deserialization_starting';
    public const DESERIALIZATION_FINISHED = 'deserialization_finished';
}