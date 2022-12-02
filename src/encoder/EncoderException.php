<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\encoder;

/**
 * @package froq\encoding\encoder
 * @class   froq\encoding\encoder\EncoderException
 * @author  Kerem Güneş
 * @since   6.0
 */
class EncoderException extends \froq\encoding\EncodingException
{
    public static function forNoInputGiven(string $class): static
    {
        return new static('No input given yet, call %s::setInput() first', $class);
    }

    public static function forInvalidType(string $type): static
    {
        return new static('Invalid type %q [valids: json, xml, gzip, zlib]', $type);
    }
}
