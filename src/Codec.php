<?php

namespace PhpCommon\Serializer;

use PhpCommon\Serializer\Metadata\Type;

interface Codec
{
    public function encode($data, Type $type = null, View $view = null) : string;
    public function decode($data, Type $type, View $view = null);
}