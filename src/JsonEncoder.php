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

use froq\encoding\{Encoder, EncoderInterface, EncoderError, EncoderException};
use Throwable;

/**
 * Json Encoder.
 * @package froq\encoding
 * @object  froq\encoding\JsonEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0
 */
final class JsonEncoder extends Encoder implements EncoderInterface
{
    /**
     * Constructor.
     * @param  any $data
     * @throws froq\encoding\EncoderException If JSON module not found.
     */
    public function __construct($data)
    {
        if (!function_exists('json_encode')) {
            throw new EncoderException('JSON module not found');
        }

        parent::__construct(Encoder::TYPE_JSON, $data);
    }

    /**
     * @inheritDoc froq\encoding\EncoderInterface
     */
    public function encode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;
        if ($data === '') { // skip empty strings
            return '""';
        }

        try {
            $data = json_encode($data,
                (int) ($options['flags'] ?? 0),
                (int) ($options['depth'] ?? 512)
            );

            if (json_last_error()) {
                $data = null;
                $error = new EncoderError(json_last_error_msg() ?: 'Unknown',
                    EncoderError::TYPE_JSON);

            }
        } catch (Throwable $e) {
            $data = null;
            $error = new EncoderError($e->getMessage(), EncoderError::TYPE_JSON);
        }

        return $data;
    }

    /**
     * @inheritDoc froq\encoding\EncoderInterface
     */
    public function decode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        if (!is_string($data)) {
            $error = new EncoderError(sprintf('String data needed for %s(), %s given',
                __method__, gettype($data)));
            return null;
        }

        if ($data === '') { // skip empty strings (that already null)
            return null;
        }

        // if 'false' given with JSON_OBJECT_AS_ARRAY in flags, simply 'false' overrides on..
        $options['assoc'] = $options['assoc'] ?? null;
        if ($options['assoc'] !== null) {
            $options['assoc'] = (bool) $options['assoc'];
        }

        try {
            $data = json_decode($data,
                       $options['assoc'],
                (int) ($options['depth'] ?? 512),
                (int) ($options['flags'] ?? 0)
            );

            if (json_last_error()) {
                $data = null;
                $error = new EncoderError(json_last_error_msg() ?: 'Unknown',
                    EncoderError::TYPE_JSON);
            }
        } catch (Throwable $e) {
            $data = null;
            $error = new EncoderError($e->getMessage(), EncoderError::TYPE_JSON);
        }

        return $data;
    }
}
