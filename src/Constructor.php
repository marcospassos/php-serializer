<?php

namespace PhpCommon\Serializer;

use PhpCommon\Serializer\Metadata\Type;

/**
 * A construct is a strategy for constructing objects during deserialization.
 *
 * There are several ways of instantiating an object. Examples includes the
 * "new" operator, "unserialize" techniques and reflection.
 *
 * @since  1.0
 *
 * @author Marcos Passos <marcos@marcospassos.com>
 */
interface Constructor
{
    /**
     * Creates a new instance of the specified type.
     *
     * @param Type $type The object type.
     *
     * @return object An instance of the requested type.
     */
    public function create(Type $type);
}