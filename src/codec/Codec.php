<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

use froq\encoding\encoder\{Encoder, EncoderError};
use froq\encoding\decoder\{Decoder, DecoderError};
use froq\common\trait\OptionTrait;

/**
 * Base codec class, creates encoder/decoder properties via `__get()` method for once
 * and provides encode/decode operations for subclasses using these properties.
 *
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\Codec
 * @author  Kerem Güneş
 * @since   6.0
 */
abstract class Codec
{
    use OptionTrait;

    /** Encoder instance. */
    private readonly Encoder $encoder;

    /** Decoder instance. */
    private readonly Decoder $decoder;

    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $this->setOptions($options);
    }

    /**
     * Forbid dynamic modifications.
     *
     * @return never
     * @throws ReadonlyPropertyError
     * @throws UndefinedPropertyError
     */
    public function __set(string $property, mixed $_): never
    {
        match ($property) {
            'encoder', 'decoder' => throw new \ReadonlyPropertyError($this, $property),
            default => throw new \UndefinedPropertyError($this, $property)
        };
    }

    /**
     * Get encoder/decoder properties creating on-demand for once.
     *
     * @return froq\encoding\encoder\Encoder|froq\encoding\decoder\Decoder
     * @throws UndefinedPropertyError
     */
    public function __get(string $property): Encoder|Decoder
    {
        return match ($property) {
            'encoder', 'decoder' => $this->$property ??= $this->create($property),
            default => throw new \UndefinedPropertyError($this, $property)
        };
    }

    /**
     * Encode.
     *
     * @param  mixed                                                                       $input
     * @param  froq\encoding\encoder\EncoderError|froq\encoding\decoder\DecoderError|null &$error
     * @return mixed|false
     */
    public function encode(mixed $input, EncoderError|DecoderError &$error = null): mixed
    {
        return $this->__get('encoder')->convert($input, $error);
    }

    /**
     * Decode.
     *
     * @param  mixed                                                                       $input
     * @param  froq\encoding\encoder\EncoderError|froq\encoding\decoder\DecoderError|null &$error
     * @return mixed|false
     */
    public function decode(mixed $input, EncoderError|DecoderError &$error = null): mixed
    {
        return $this->__get('decoder')->convert($input, $error);
    }

    /**
     * Create encoder/decoder properties.
     *
     * @throws froq\encoding\codec\CodecException
     */
    private function create(string $name): Encoder|Decoder
    {
        // Eg: ["encoder" => ..] for encoder.
        $options = (array) $this->getOption($name);

        if (!$class = array_pluck($options, 'class')) {
            throw CodecException::forAbsentClassOption(static::class);
        }

        if (!class_exists($class)) {
            throw CodecException::forAbsentClass($class);
        }

        if ($name === 'encoder' && !class_extends($class, Encoder::class)) {
            throw CodecException::forInvalidSubclass($class, Encoder::class);
        } elseif ($name === 'decoder' && !class_extends($class, Decoder::class)) {
            throw CodecException::forInvalidSubclass($class, Decoder::class);
        }

        $options = array_options($options, $class::getDefaultOptions());

        return new $class($options);
    }
}
