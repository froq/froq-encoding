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

    /**
     * Constructor.
     *
     * @param array|null $options
     * @param array|null $optionsDefault
     */
    public function __construct(array $options = null, array $optionsDefault = null)
    {
        $optionsDefault ??= self::getDefaultOptions();

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
