<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{AbstractEncoder, EncoderError, EncodingException};
use Throwable;

/**
 * Gzip Encoder.
 *
 * @package froq\encoding
 * @object  froq\encoding\GzipEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
final class GzipEncoder extends AbstractEncoder
{
    /**
     * Constructor.
     * @param  any $data
     * @throws froq\encoding\EncodingException
     */
    public function __construct($data)
    {
        if (!extension_loaded('zlib')) {
            throw new EncodingException('zlib module not loaded');
        }

        parent::__construct(Encoder::TYPE_GZIP, $data);
    }

    /**
     * @inheritDoc froq\encoding\AbstractEncoder
     */
    public function encode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        if (!is_string($data)) {
            $error = new EncoderError("String data needed for '%s()', '%s' given",
                [__method__, gettype($data)], EncoderError::GZIP);
            return null;
        }

        try {
            $result = gzencode($data,
                (int) ($options['level'] ?? -1),
                (int) ($options['mode'] ?? FORCE_GZIP)
            );

            if ($result === false) {
                throw new EncoderError(error_message() ?: 'unknown');
            }

            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::GZIP);

            return null;
        }
    }

    /**
     * @inheritDoc froq\encoding\AbstractEncoder
     */
    public function decode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        if (!is_string($data)) {
            $error = new EncoderError("String data needed for '%s()', '%s' given",
                [__method__, gettype($data)], EncoderError::GZIP);
            return null;
        }

        // Skip empty strings.
        if ($data === '') {
            return null;
        }

        try {
            $result = gzdecode($data,
                (int) ($options['length'] ?? 0)
            );

            if ($result === false) {
                throw new EncoderError(error_message() ?: 'unknown');
            }

            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::GZIP);

            return null;
        }
    }
}
