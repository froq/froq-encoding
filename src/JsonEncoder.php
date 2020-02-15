<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{AbstractEncoder, EncoderError, EncodingException};
use Throwable;

/**
 * Json Encoder.
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
     * @throws froq\encoding\EncodingException If json module not loaded.
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
                throw new EncoderError(json_last_error_msg() ?: 'Unknown JSON error');
            }
            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e->getMessage(), null, EncoderError::TYPE_JSON);
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
            $error = new EncoderError('String data needed for "%s()", "%s" given',
                [__method__, gettype($data)]);
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
                throw new EncoderError(json_last_error_msg() ?: 'Unknown JSON error');
            }
            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e->getMessage(), null, EncoderError::TYPE_JSON);
            return null;
        }
    }
}
