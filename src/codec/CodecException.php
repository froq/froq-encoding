<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

/**
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\CodecException
 * @author  Kerem Güneş
 * @since   6.0
 */
class CodecException extends \froq\encoding\EncodingException
{
    public static function forAbsentClassOption(string $class): static
    {
        return new static('Option "class" must be given in %s as a valid class', $class);
    }

    public static function forAbsentClass(string $class): static
    {
        return new static('No class exists such %s', $class);
    }

    public static function forInvalidSubclass(string $class, string $parent): static
    {
        return new static('Class %s must extend class %s', [$class, $parent]);
    }
}
