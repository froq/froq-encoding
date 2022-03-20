<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\GZipEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class GZipEncoder extends Encoder
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
            $this->input = gzencode(
                $this->input,
                $this->options['level'],
                FORCE_GZIP
            );

            if ($this->input === false) {
                throw new \LastError();
            }
        } catch (\Throwable $e) {
            $error = new EncoderError(
                $e->getMessage(), code: EncoderError::GZIP, cause: $e
            );
        }

        return ($error == null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('gzip', $input);
    }
}