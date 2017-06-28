<?php

namespace PhpCommon\Serializer;

use InvalidArgumentException;

/**
 * Thrown to indicate the format requested for serialization or deserialization
 * is not supported.
 *
 * @since  1.0
 *
 * @author Marcos Passos
 */
class UnsupportedFormatException extends InvalidArgumentException
{
}