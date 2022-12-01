<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\decoder;

use froq\common\trait\{OptionTrait, InputTrait, OutputTrait};

/**
 * Base decoder class, provides `convert()` method and `decode()` method as abstract
 * with other input/output related methods.
 *
 * @package froq\encoding\decoder
 * @class   froq\encoding\decoder\Decoder
 * @author  Kerem Güneş
 * @since   6.0
 */
abstract class Decoder
{
    use OptionTrait, InputTrait, OutputTrait;

    /** Decoder error. */
    protected ?DecoderError $error = null;

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
     * @return froq\encoding\decoder\DecoderError|null
     */
    public function error(): DecoderError|null
    {
        return $this->error;
    }

    /**
     * Convert.
     *
     * @param  mixed                                    $input
     * @param  froq\encoding\decoder\DecoderError|null &$error
     * @return mixed|false
     */
    public function convert(mixed $input, DecoderError &$error = null): mixed
    {
        $this->setInput($input);

        if (!$this->decode()) {
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
     * Ensure `setInput()` called to set `$input` property for `decode()` calls.
     *
     * @return void
     * @throws froq\encoding\decoder\DecoderException
     */
    protected function inputCheck(): void
    {
        if (!$this->hasInput()) {
            throw new DecoderException(
                'No input given yet, call %s::setInput() first',
                static::class
            );
        }
    }

    /**
     * Throw occured error on `decode()` calls if option `throwsErrors` not false.
     *
     * @return void
     * @throws froq\encoding\decoder\DecoderError
     */
    protected function errorCheck(): void
    {
        if ($this->error && $this->options['throwErrors']) {
            throw $this->error;
        }
    }

    /**
     * Decode.
     *
     * @return bool
     * @causes froq\encoding\decoder\{DecoderError|DecoderException}
     */
    abstract public function decode(): bool;
}
