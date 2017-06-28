<?php

namespace PhpCommon\Serializer;

use PhpCommon\Serializer\Event\TypeEnteredEvent;
use PhpCommon\Serializer\Event\TypeLeftEvent;
use PhpCommon\Serializer\Exclusion\NoExclusionStrategy;
use PhpCommon\Serializer\Metadata\Type;
use SeekableIterator;
use BadMethodCallException;
use ArrayIterator;

/**
 * Abstract context of a serialization or deserialization operation.
 *
 * @since  1.0
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
abstract class Context
{
    public const SERIALIZATION = 'serialization';
    public const DESERIALIZATION = 'deserialization';

    private $exclusionStrategy;
    private $properties;
    private $stack;
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher, ExclusionStrategy $exclusionStrategy = null, array $properties = [])
    {
        if ($exclusionStrategy === null) {
            $exclusionStrategy = new NoExclusionStrategy();
        }

        $this->dispatcher = $dispatcher;
        $this->exclusionStrategy = $exclusionStrategy;
        $this->properties = $properties;
        $this->stack = [];
    }

    public function hasProperty(string $name) : bool
    {
        return array_key_exists($name, $this->properties);
    }

    public function getProperties() : array
    {
        return $this->properties;
    }

    public function getProperty(string $name, $default = null)
    {
        if (!$this->hasProperty($name)) {
            return $default;
        }

        return $this->properties[$name];
    }

    abstract public function getDirection() : string;
    abstract public function getFormat() : string;

    public function getData()
    {
        if (empty($this->stack)) {
            throw new BadMethodCallException('The stack is empty.');
        }

        return $this->stack[0][0];
    }

    public function getTraversalStack() : SeekableIterator
    {
        return new ArrayIterator($this->stack);
    }

    public function getTraversalDepth() : int
    {
        return count($this->stack);
    }

    /**
     * Returns the exclusion strategy to decide whether or not a property or
     * top-level class should be serialized or deserialized.
     *
     * @return ExclusionStrategy The exclusion strategy.
     */
    public function getExclusionStrategy(): ExclusionStrategy
    {
        return $this->exclusionStrategy;
    }

    protected function accept($data, Type $type, callable $callback)
    {
        $event = $this->enterType($data, $type);

        $data = $event->getData();
        $type = $event->getType();

        $pop = true;
        $tuple = [$data, $type];

        if (!empty($this->stack)) {
            $current = $this->stack[0];

            if ($current === $tuple) {
                throw new \RuntimeException('Infinite loop');
            }

            if ($current[0] === $data) {
                $pop = false;
                array_shift($this->stack);
            }
        }

        array_unshift($this->stack, [$data, $type]);

        $result = $callback($data, $type);

        if ($pop) {
            array_shift($this->stack);
        }

        $event = $this->leaveType($result, $type);

        return $event->getData();
    }

    protected function enterType($data, Type $type) : TypeEnteredEvent
    {
        $event = new TypeEnteredEvent($this, $data, $type);
        $direction = $this->getDirection();
        $format = $this->getFormat();

        $names = [
            sprintf('%s.type_entered', $direction),
            sprintf('%s.%s.type_entered', $format, $direction),
        ];

        foreach ($names as $name) {
            $this->dispatcher->dispatch($name, $event);
        }

        return $event;
    }

    protected function leaveType($data, Type $type) : TypeLeftEvent
    {
        $event = new TypeLeftEvent($this, $data, $type);

        $direction = $this->getDirection();
        $format = $this->getFormat();

        $names = [
            sprintf('%s.type_left', $direction),
            sprintf('%s.%s.type_left', $format, $direction),
        ];

        foreach ($names as $name) {
            $this->dispatcher->dispatch($name, $event);
        }

        return $event;
    }
}