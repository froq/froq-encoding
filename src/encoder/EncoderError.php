<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\encoder;

/**
 * @package froq\encoding\encoder
 * @class   froq\encoding\encoder\EncoderError
 * @author  Kerem Güneş
 * @since   6.0
 */
class EncoderError extends \froq\encoding\EncodingError
{
    /** Error codes. */
    public const GZIP = 1, ZLIB = 2, JSON = 3, XML = 4;
}
