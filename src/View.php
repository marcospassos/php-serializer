<?php

namespace PhpCommon\Serializer;

interface View
{
    public function getFormat() : string;
    public function buildContext(ContextBuilder $builder) : void;
}