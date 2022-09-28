<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
 * ZLib encoder class, provides encode operations for ZLib related jobs.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\ZLibEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class ZLibEncoder extends Encoder
{
    /** @var array */
    protected static array $optionsDefault = ['level' => -1];

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(): bool
    {
        $this->inputCheck();

        // Wrap for type errors etc.
        try {
            $this->output = zlib_encode(
                $this->input,
                ZLIB_ENCODING_DEFLATE,
                $this->options['level']
            );

            if ($this->output === false) {
                throw new \LastError();
            }
        } catch (\Throwable $e) {
            $this->error = new EncoderError($e, code: EncoderError::ZLIB);

            $this->errorCheck();
        }

        return ($this->error == null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('zlib', $input);
    }
}
