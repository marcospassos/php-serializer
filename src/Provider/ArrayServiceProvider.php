<?php

namespace PhpCommon\Serializer\Provider;

use PhpCommon\Serializer\ServiceProvider;
use InvalidArgumentException;

class ArrayServiceProvider implements ServiceProvider
{
    private $services;

    public function __construct(array $services)
    {
        foreach ($services as $id => $service) {
            if (!is_object($service)) {
                throw new InvalidArgumentException(sprintf(
                    'The service %d is not an object.',
                    $id
                ));
            }

            if (!is_string($id)) {
                $id = get_class($service);
            }

            $this->services[$id] = $service;
        }
    }

    public function getService(string $id)
    {
        if (!isset($this->services[$id])) {
            throw new \InvalidArgumentException(sprintf(
                'The service %s does not exist.',
                $id
            ));
        }

        return $this->services[$id];
    }
}