<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

use froq\common\Error;

/**
 * Encoder Error.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\EncoderError
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
class EncoderError extends Error
{
    /**
     * Codes.
     * @const int
     */
    public final const GZIP = 1,
                       JSON = 2,
                       XML  = 3;
}
