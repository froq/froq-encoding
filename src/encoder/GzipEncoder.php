<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
 * Gzip Encoder.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\GzipEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class GzipEncoder extends Encoder
{
    /** @var array */
    protected static array $optionsDefault = [
        'level'  => -1, 'mode' => FORCE_GZIP,
        'length' => 0,
    ];

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(EncoderError &$error = null): bool
    {
        $error = null;

        // Wrap for type errors etc.
        try {
            $input = gzencode(
                $this->input,
                $this->options['level'],
                $this->options['mode'],
            );

            if ($input === false) {
                throw new \Error(error_message());
            }

            $this->input = $input;
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::GZIP);
        }

        return ($error == null);
    }

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function decode(EncoderError &$error = null): bool
    {
        $error = null;

        // Wrap for type errors etc.
        try {
            $input = gzdecode(
                $this->input,
                $this->options['length'],
            );

            if ($input === false) {
                throw new \Error(error_message());
            }

            $this->input = $input;
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::GZIP);
        }

        return ($error == null);
    }
}
