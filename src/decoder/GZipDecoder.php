<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\decoder;

/**
 * GZip decoder class, provides decode operations for GZip related jobs.
 *
 * @package froq\encoding\decoder
 * @class   froq\encoding\decoder\GZipDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class GZipDecoder extends Decoder
{
    /** Default options. */
    protected static array $optionsDefault = ['length' => 0];

    /**
     * @inheritDoc froq\encoding\decoder\Decoder
     */
    public function decode(mixed ...$options): bool
    {
        $this->error = null;
        $this->inputCheck();

        $options = $this->options($options);

        // Wrap for type errors etc.
        try {
            $this->output = gzdecode(
                $this->input,
                $options['length']
            );

            if ($this->output === false) {
                throw new \LastError();
            }
        } catch (\Throwable $e) {
            $this->error = new DecoderError($e, code: DecoderError::GZIP);
            $this->errorCheck();
        }

        return ($this->error === null);
    }
}
