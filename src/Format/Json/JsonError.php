<?php

namespace PhpCommon\Serializer\Format\Json;

final class JsonError
{
    private function __construct()
    {
    }

    /**
     * Returns the error message of a JSON error code.
     *
     * @param int $error The error code
     *
     * @return string The error message
     */
    public static function getErrorMessage(int $error) : string
    {
        switch ($error) {
            case JSON_ERROR_NONE:
                return 'No error has occurred';
            case JSON_ERROR_DEPTH:
                return 'The maximum stack depth has been exceeded';
            case JSON_ERROR_STATE_MISMATCH:
                return 'Invalid or malformed JSON';
            case JSON_ERROR_CTRL_CHAR:
                return 'Control character error, possibly incorrectly encoded';
            case JSON_ERROR_SYNTAX:
                return 'Syntax error';
            case JSON_ERROR_UTF8:
                return 'Malformed UTF-8 characters, possibly incorrectly encoded';
            case JSON_ERROR_RECURSION:
                    return 'One or more recursive references in the value to be encoded';
            case JSON_ERROR_INF_OR_NAN:
                return 'One or more NAN or INF values in the value to be encoded';
            case JSON_ERROR_UNSUPPORTED_TYPE:
                return 'A value of a type that cannot be encoded was given';
            case JSON_ERROR_INVALID_PROPERTY_NAME:
                return 'A property name that cannot be encoded was given';
            case JSON_ERROR_UTF16:
                return 'Malformed UTF-16 characters, possibly incorrectly encoded';
        }

        return 'Unknown error';
    }
}