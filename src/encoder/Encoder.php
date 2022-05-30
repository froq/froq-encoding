<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

use froq\common\trait\{OptionTrait, InputTrait, OutputTrait};

/**
 * Base encoder class, provides `convert()` method and `encode()` method as abstract
 * with other input/output related methods
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\Encoder
 * @author  Kerem Güneş
 * @since   6.0
 */
abstract class Encoder
{
    use OptionTrait, InputTrait, OutputTrait;

    /** @var array */
    protected static array $optionsDefault = ['throwErrors' => false];

    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Merge with subclass defaults.
        $optionsDefault = array_options(
            self::$optionsDefault,
            static::$optionsDefault ?? null
        );

        $this->setOptions(array_options($options, $optionsDefault));
    }

    /**
     * Convert.
     *
     * @param  froq\encoding\encoder\EncoderError|null &$error
     * @return bool
     * @causes froq\encoding\encoder\EncoderException
     */
    public function convert(mixed $input, EncoderError &$error = null): mixed
    {
        ($that = clone $this)->setInput($input)->encode($error);

        return $that->getOutput();
    }

    /**
     * Ensure `setInput()` called to set `$input` property for `encode()` calls.
     *
     * @return void
     * @throws froq\encoding\encoder\EncoderException
     */
    protected function inputCheck(): void
    {
        if (!$this->hasInput()) {
            throw new EncoderException(
                'No input given yet, call %s::setInput() first',
                static::class
            );
        }
    }

    /**
     * Handle given error for `encode()` calls, if error is not null and
     * option `throwsErrors` not false.
     *
     * @param  froq\encoding\encoder\EncoderError $error
     * @return void
     * @throws froq\encoding\encoder\EncoderError
     */
    protected function errorCheck(EncoderError $error): void
    {
        if ($this->options['throwErrors']) {
            throw $error;
        }
    }

    /**
     * Check encoded status of given input.
     *
     * @param  string $type
     * @param  mixed  $input
     * @return bool
     * @throws froq\encoding\encoder\EncoderException
     */
    protected static function isEncoded(string $type, mixed $input): bool
    {
        // Special case of JSON stuff.
        if ($type == 'json' && is_string($input) && (
            is_numeric($input) || equals($input, 'null', 'true', 'false')
        )) {
            return true;
        }

        return match ($type) {
            'json' => is_string($input)
                   && isset($input[0], $input[-1])
                   && (
                        ($input[0] . $input[-1]) == '{}' ||
                        ($input[0] . $input[-1]) == '[]' ||
                        ($input[0] . $input[-1]) == '""'
                      ),
            'xml' => is_string($input)
                  && isset($input[0], $input[-1])
                  && (
                        ($input[0] . $input[-1]) == '<>'
                     ),
            'gzip' => is_string($input)
                   && str_starts_with($input, "\x1F\x8B"), // Constant.

            'zlib' => is_string($input)
                   && (
                        str_starts_with($input, "\x78\x9C") || // Default.
                        str_starts_with($input, "\x78\xDA") || // Best.
                        str_starts_with($input, "\x78\x01")    // None/low.
                      ),

            default => throw new EncoderException(
                'Invalid type `%s` [valids: json, xml, gzip, zlib]', $type
            )
        };
    }

    /**
     * Encode.
     *
     * @param  froq\encoding\encoder\EncoderError|null &$error
     * @return bool
     * @causes froq\encoding\encoder\{EncoderError|EncoderException}
     */
    abstract public function encode(EncoderError &$error = null): bool;
}
