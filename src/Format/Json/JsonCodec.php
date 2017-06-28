<?php

namespace PhpCommon\Serializer\Format\Json;

use PhpCommon\Serializer\Codec;
use PhpCommon\Serializer\DecodingFailedException;
use PhpCommon\Serializer\EncodingFailedException;
use PhpCommon\Serializer\Event\DeserializationStartedEvent;
use PhpCommon\Serializer\Event\SerializationStartedEvent;
use PhpCommon\Serializer\EventDispatcher;
use PhpCommon\Serializer\Metadata\Type;
use PhpCommon\Serializer\ServiceProvider;
use PhpCommon\Serializer\View;

class JsonCodec implements Codec
{
    private $provider;
    private $dispatcher;
    private $defaultSerializers;
    private $defaultDeserializers;

    public function __construct(
        ServiceProvider $provider,
        EventDispatcher $dispatcher,
        array $defaultSerializers = [],
        array $defaultDeserializers = []
    ) {
        $this->provider = $provider;
        $this->dispatcher = $dispatcher;
        $this->defaultSerializers = $defaultSerializers;
        $this->defaultDeserializers = $defaultDeserializers;
    }

    public function encode($data, Type $type = null, View $view = null): string
    {
        $builder = new JsonSerializationContextBuilder(
            $this->provider,
            $this->dispatcher,
            $this->defaultSerializers
        );

        if ($view !== null) {
            $view->buildContext($builder);
        }

        return $this->serialize($data, $type, $builder->create());
    }

    public function decode($data, Type $type, View $view = null)
    {
        $builder = new JsonDeserializationContextBuilder(
            $this->provider,
            $this->dispatcher,
            $this->defaultDeserializers
        );

        if ($view !== null) {
            $view->buildContext($builder);
        }

        return $this->deserialize($data, $type, $builder->create());
    }

    private function serialize($data, Type $type = null, JsonSerializationContext $context)
    {
        $event = new SerializationStartedEvent($context, $data, $type);
        $this->dispatcher->dispatch('serialization.started', $event);
        $this->dispatcher->dispatch('json.serialization.started', $event);

        $serialized = $context->serialize($data, $type);
        $result = $this->jsonEncode($serialized, $context);

        return $result;
    }

    private function deserialize($data, Type $type, JsonDeserializationContext $context)
    {
        $event = new DeserializationStartedEvent($context, $data, $type);
        $this->dispatcher->dispatch('deserialization.started', $event);
        $this->dispatcher->dispatch('json.deserialization.started', $event);

        $decoded = $this->jsonDecode($data, $context);

        return $context->deserialize($decoded, $type);
    }

    private function jsonEncode($data, JsonSerializationContext $context)
    {
        $result = json_encode($data);

        if (JSON_ERROR_NONE !== ($lastError = json_last_error())) {
            throw new EncodingFailedException(
                sprintf(
                    'The data could not be encoded as JSON: %s',
                    JsonError::getErrorMessage($lastError)
                ),
                $lastError
            );
        }

        return $result;
    }

    private function jsonDecode(string $data, JsonDeserializationContext $context)
    {
        $result = json_decode($data, true);

        if (JSON_ERROR_NONE !== ($lastError = json_last_error())) {
            throw new DecodingFailedException(
                sprintf(
                    'The JSON data could not be decoded: %s',
                    JsonError::getErrorMessage($lastError)
                ),
                $lastError
            );
        }

        return $result;
    }
}