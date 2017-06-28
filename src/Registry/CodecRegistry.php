<?php

namespace PhpCommon\Serializer\Registry;

use PhpCommon\ResourceResolver\Locator\ArrayRegistry;
use PhpCommon\Serializer\Codec;

class CodecRegistry extends ArrayRegistry
{
    public function __construct(array $codecs = [])
    {
        parent::__construct($codecs);
    }

    public function get($name) : Codec
    {
        return parent::get($name);
    }
}