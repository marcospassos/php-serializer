<?php

namespace PhpCommon\Serializer\Metadata;

use Serializable;

class Type implements Serializable
{
    private const PATTERN = '/(?P<name>[^<>,]+)(?:<(?P<params>(?0)(?:,(?0))*)>)?/';

    protected const PRIMITIVES = [
        'array',
        'string',
        'boolean',
        'integer',
        'float',
        'resource',
    ];

    protected const NUMBERS = [
        'integer',
        'float'
    ];

    protected const SCALAR = [
        'string',
        'boolean',
        'integer',
        'float',
    ];

    private $name;
    private $parameters = [];
    private $singletons = [];

    public function __construct(string $name, Type ...$parameters)
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getParameterCount() : int
    {
        return count($this->parameters);
    }

    public function getParameters() : array
    {
        return $this->parameters;
    }

    public function getParameter(int $index) : Type
    {
        if (!isset($this->parameters[$index])) {
            throw new \InvalidArgumentException('Invalid parameter '. $index);
        }

        return $this->parameters[$index];
    }

    public function equals($type) : bool
    {
        return (string) $this === (string) $type;
    }

    public function isScalar() : bool
    {
        return in_array(strtolower($this->name), self::SCALAR);
    }

    public function isNumber() : bool
    {
        return in_array(strtolower($this->name), self::NUMBERS);
    }

    public function isArray() : bool
    {
        return strtolower($this->name) === 'array';
    }

    public function isObject() : bool
    {
        if (in_array(strtolower($this->name), self::PRIMITIVES, true)) {
            return false;
        }

        return class_exists($this->name);
    }

    public function isArrayOf($type, $covariant = false) : bool
    {
        if (!$this->isArray() || $this->getParameterCount() !== 1) {
            return false;
        }

        if (!$type instanceof Type) {
            $type = self::parse($type);
        }

        $elementType = $this->getParameter(0);

        if ($covariant) {
            return $elementType->isCovariant($type);
        }

        return $type->equals($elementType);
    }

    public function isCovariant($type) : bool
    {
        if ($type === $this) {
            return true;
        }

        if (!$type instanceof self) {
            $type = self::parse($type);
        }

        if ($this->getParameterCount() !== $type->getParameterCount()) {
            return false;
        }

        if (!$this->matchCovariance($this, $type)) {
            return false;
        }

        foreach ($this->parameters as $index => $parameter) {
            if (!$parameter->isCovariant($type->getParameter($index))) {
                return false;
            }
        }

        return true;
    }

    protected function matchCovariance(Type $left, Type $right)
    {
        if ($left === $right) {
            return true;
        }

        if (in_array($left->name, self::PRIMITIVES)) {
            return $left->name === $right->name;
        }

        if ($left->name === 'number') {
            return in_array($right->name, self::NUMBERS);
        }

        return is_a($left->name, $right->name, true);
    }

    public function serialize()
    {
        return serialize([$this->name, $this->parameters]);
    }

    public function unserialize($serialized) : void
    {
        list($this->name, $this->parameters) = unserialize($serialized);
    }

    public function __toString() : string
    {
        $result = $this->name;

        if (!empty($this->parameters)) {
            $result .= sprintf('<%s>', implode(',', $this->parameters));
        }

        return $result;
    }

    public static function parse(string $type) : Type {
        if (!preg_match(self::PATTERN, $type, $match)) {
            throw new \InvalidArgumentException('Malformed type');
        }

        $params = [];
        if (isset($match['params'])) {
            $parser = [self::class, 'parse'];
            $params = array_map($parser, explode(',', $match['params']));
        }

        return new Type($match['name'], ...$params);
    }

    public static function detect($value)
    {
        if (is_object($value)) {
            return new self(get_class($value));
        }

        return new self(gettype($value));
    }
}