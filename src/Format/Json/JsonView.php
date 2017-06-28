<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\View;

abstract class JsonView implements View
{
    public const FORMAT = 'json';

    final public function getFormat(): string
    {
        return self::FORMAT;
    }
}