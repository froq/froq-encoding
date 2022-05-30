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
     * @causes froq\encoding\decoder\DecoderException
     */
    protected function ensureInput(): void
    {
        $this->checkInput(DecoderException::class);
    }

    /**
     * Decode.
     *
     * @param  froq\encoding\decoder\DecoderError|null &$error
     * @return bool
     * @causes froq\encoding\decoder\DecoderException
     */
    abstract public function decode(DecoderError &$error = null): bool;
}
