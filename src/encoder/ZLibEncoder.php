<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
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
    public function encode(EncoderError &$error = null): bool
    {
        $this->ensureInput();

        $error = null;

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
            $error = new EncoderError(
                $e->getMessage(), code: EncoderError::ZLIB, cause: $e
            );
        }

        return ($error == null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('zlib', $input);
    }
}
