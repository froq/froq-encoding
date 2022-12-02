<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\decoder;

/**
 * @package froq\encoding\decoder
 * @class   froq\encoding\decoder\DecoderException
 * @author  Kerem Güneş
 * @since   6.0
 */
class DecoderException extends \froq\encoding\EncodingException
{
    public static function forNoInputGiven(string $class): static
    {
        return new static('No input given yet, call %s::setInput() first', $class);
    }
}
