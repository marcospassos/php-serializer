<?php

namespace PhpCommon\Serializer;

interface ServiceProvider
{
    public function getService(string $id);
}