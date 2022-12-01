<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\encoder;

use froq\common\trait\{OptionTrait, InputTrait, OutputTrait};

/**
 * Base encoder class, provides `convert()` method and `encode()` method as abstract
 * with other input/output related methods
 *
 * @package froq\encoding\encoder
 * @class   froq\encoding\encoder\Encoder
 * @author  Kerem Güneş
 * @since   6.0
 */
abstract class Encoder
{
    use OptionTrait, InputTrait, OutputTrait;

    /** Encoder error. */
    protected ?EncoderError $error = null;

    /** Default options. */
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
            static::$optionsDefault,
            self::$optionsDefault
        );

        $this->setOptions(array_options($options, $optionsDefault));
    }

    /**
     * Get error property.
     *
     * @return froq\encoding\encoder\EncoderError|null
     */
    public function error(): EncoderError|null
    {
        return $this->error;
    }

    /**
     * Convert.
     *
     * @param  mixed                                    $input
     * @param  froq\encoding\encoder\EncoderError|null &$error
     * @return mixed|false
     */
    public function convert(mixed $input, EncoderError &$error = null): mixed
    {
        $this->setInput($input);

        if (!$this->encode()) {
            $error = $this->error();
            return false;
        }

        return $this->getOutput();
    }

    /**
     * Get options, merge if new given or select only given keys.
     *
     * @param  array      $options
     * @param  array|null $keys
     * @return array
     */
    public function options(array $options = [], array $keys = null): array
    {
        // Merge with given options on runtime.
        $options = array_merge($this->options, $options);

        // Select given only keys if any given.
        $keys && $options = array_select($options, $keys, combine: true);

        return $options;
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
     * Throw occured error on `encode()` calls if option `throwsErrors` not false.
     *
     * @return void
     * @throws froq\encoding\encoder\EncoderError
     */
    protected function errorCheck(): void
    {
        if ($this->error && $this->options['throwErrors']) {
            throw $this->error;
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
        if ($type === 'json' && is_string($input) && (
            is_numeric($input) || equals($input, 'null', 'true', 'false')
        )) {
            return true;
        }

        return match ($type) {
            'json' => is_string($input)
                   && isset($input[0], $input[-1])
                   && (
                        ($input[0] . $input[-1]) === '{}' ||
                        ($input[0] . $input[-1]) === '[]' ||
                        ($input[0] . $input[-1]) === '""'
                      ),
            'xml' => is_string($input)
                  && isset($input[0], $input[-1])
                  && (
                        ($input[0] . $input[-1]) === '<>'
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
                'Invalid type %q [valids: json, xml, gzip, zlib]', $type
            )
        };
    }

    /**
     * Encode.
     *
     * @return bool
     * @causes froq\encoding\encoder\{EncoderError|EncoderException}
     */
    abstract public function encode(): bool;
}
