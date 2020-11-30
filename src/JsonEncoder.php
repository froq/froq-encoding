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
 * Json Encoder.
 *
 * @package froq\encoding
 * @object  froq\encoding\JsonEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
final class JsonEncoder extends AbstractEncoder
{
    /**
     * Constructor.
     * @param  any $data
     * @throws froq\encoding\EncodingException
     */
    public function __construct($data)
    {
        if (!extension_loaded('json')) {
            throw new EncodingException('json module not loaded');
        }

        parent::__construct(Encoder::TYPE_JSON, $data);
    }

    /**
     * @inheritDoc froq\encoding\AbstractEncoder
     */
    public function encode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        // Skip empty strings.
        if ($data === '') {
            return '""';
        }

        try {
            $result = json_encode($data,
                (int) ($options['flags'] ?? 0),
                (int) ($options['depth'] ?? 512)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg() ?: 'unknown');
            }

            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

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
                [__method__, gettype($data)], EncoderError::JSON);
            return null;
        }

        // Skip empty strings.
        if ($data === '') {
            return null;
        }

        // If false given with JSON_OBJECT_AS_ARRAY in flags, simply false overrides on.
        $options['assoc'] = $options['assoc'] ?? null;
        if ($options['assoc'] !== null) {
            $options['assoc'] = (bool) $options['assoc'];
        }

        try {
            $result = json_decode($data,
                       $options['assoc'],
                (int) ($options['depth'] ?? 512),
                (int) ($options['flags'] ?? 0)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg() ?: 'unknown');
            }

            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

            return null;
        }
    }
}
