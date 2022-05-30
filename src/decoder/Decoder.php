<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

use froq\common\trait\{OptionTrait, InputTrait, OutputTrait};

/**
 * Base decoder class, provides `convert()` method and `decode()` method as abstract
 * with other input/output related methods.
 *
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\Decoder
 * @author  Kerem Güneş
 * @since   6.0
 */
abstract class Decoder
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
     * @param  mixed                                    $input
     * @param  froq\encoding\decoder\DecoderError|null &$error
     * @return mixed
     */
    public function convert(mixed $input, DecoderError &$error = null): mixed
    {
        ($that = clone $this)->setInput($input)->decode($error);

        return $that->getOutput();
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
     * Handle given error for `decode()` calls, if error is not null and
     * option `throwsErrors` not false.
     *
     * @param  froq\encoding\decoder\DecoderError $error
     * @return void
     * @throws froq\encoding\decoder\DecoderError
     */
    protected function errorCheck(DecoderError $error): void
    {
        if ($this->options['throwErrors']) {
            throw $error;
        }
    }

    /**
     * Decode.
     *
     * @param  froq\encoding\decoder\DecoderError|null &$error
     * @return bool
     * @causes froq\encoding\decoder\{DecoderError|DecoderException}
     */
    abstract public function decode(DecoderError &$error = null): bool;
}
