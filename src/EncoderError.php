<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\common\Error;

/**
 * Encoder Error.
 *
 * @package froq\encoding
 * @object  froq\encoding\EncoderError
 * @author  Kerem Güneş
 * @since   4.0
 */
final class EncoderError extends Error
{
    /**
     * Types.
     * @const int
     */
    public const JSON = 1,
                 XML  = 2,
                 GZIP = 3;
}
