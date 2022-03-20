<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

/**
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\DecoderError
 * @author  Kerem Güneş
 * @since   6.0
 */
class DecoderError extends \froq\encoding\EncodingError
{
    /**
     * Codes.
     * @const int
     */
    public final const GZIP = 1, ZLIB = 2,
                       JSON = 3, XML  = 4;
}
