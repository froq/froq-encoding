<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\codec;

use froq\encoding\encoder\{Encoder, EncoderError};
use froq\encoding\decoder\{Decoder, DecoderError};
use froq\common\trait\{OptionTrait, SealTrait};

/**
 * @package froq\encoding\codec
 * @object  froq\encoding\codec\Codec
 * @author  Kerem Güneş
 * @since   6.0
 * @internal
 */
abstract class Codec
{
    use OptionTrait, SealTrait;

    /** @var froq\encoding\encoder\Encoder */
    private readonly Encoder $encoder;

    /** @var froq\encoding\decoder\Decoder */
    private readonly Decoder $decoder;

    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $this->setOptions($options);

        // Seal for only allowed codec classes.
        self::seal([GZipCodec::class, ZLibCodec::class, JsonCodec::class, XmlCodec::class]);
    }

    /**
     * Forbid dynamic modifications.
     *
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
     * @return mixed
     */
    public function encode(mixed $input, EncoderError|DecoderError &$error = null): mixed
    {
        ($encoder = $this->__get('encoder'))->setInput($input)->encode($error);

        return $encoder->getInput();
    }

    /**
     * Decode.
     *
     * @param  mixed                                                                       $input
     * @param  froq\encoding\encoder\EncoderError|froq\encoding\decoder\DecoderError|null &$error
     * @return mixed
     */
    public function decode(mixed $input, EncoderError|DecoderError &$error = null): mixed
    {
        ($decoder = $this->__get('decoder'))->setInput($input)->decode($error);

        return $decoder->getInput();
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
            throw new CodecException(
                'Option `class` must be given in %s as a valid class',
                static::class
            );
        }

        $options = array_options($options, $class::getDefaultOptions());

        return new $class($options);
    }
}
