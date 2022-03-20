<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

/**
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\GZipDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class GZipDecoder extends Decoder
{
    /** @var array */
    protected static array $optionsDefault = ['length' => 0];

    /**
     * @inheritDoc froq\encoding\decoder\Decoder
     */
    public function decode(DecoderError &$error = null): bool
    {
        $this->ensureInput();

        $error = null;

        // Wrap for type errors etc.
        try {
            $this->input = gzdecode(
                $this->input,
                $this->options['length']
            );

            if ($this->input === false) {
                throw new \LastError();
            }
        } catch (\Throwable $e) {
            $error = new DecoderError(
                $e->getMessage(), code: DecoderError::GZIP, cause: $e
            );
        }

        return ($error == null);
    }
}
