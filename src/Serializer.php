<?php

namespace PhpCommon\Serializer;

use Croct\Cct\Locator\ResourceNotFoundException;
use PhpCommon\Serializer\Metadata\Type;
use PhpCommon\Serializer\Registry\CodecRegistry;

class Serializer
{
    private $codecs;

    public function __construct(CodecRegistry $codecs)
    {
        $this->codecs = $codecs;
    }

    public function serialize($data, $view, string $type = null) : string
    {
        list($format, $type, $view) = $this->normalize($view, $type);

        $codec = $this->getCodec($format);

        return $codec->encode($data, $type, $view);
    }

    public function deserialize($data, string $type, $view)
    {
        list($format, $type, $view) = $this->normalize($view, $type);

        $codec = $this->getCodec($format);

        return $codec->decode($data, $type, $view);
    }

    private function getCodec(string $format) : Codec
    {
        try {
            return $this->codecs->get($format);
        } catch (ResourceNotFoundException $exception) {
            throw new UnsupportedFormatException(sprintf(
                'No codec available for format "%s".',
                $format
            ));
        }
    }

    private function normalize($view, string $type = null) : array
    {
        if ($type !== null) {
            $type = new Type($type);
        }

        if ($view instanceof View) {
            return [$view->getFormat(), $type, $view];
        }

        return [(string) $view, $type, null];
    }
}