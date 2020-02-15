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
 * Gzip Encoder.
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
            $error = new EncoderError('String data needed for "%s()", "%s" given',
                [__method__, gettype($data)], EncoderError::GZIP);
            return null;
        }

        // Skip empty strings.
        if ($data === '') {
            return null;
        }

        try {
            $result = gzencode($data,
                (int) ($options['level'] ?? -1),
                (int) ($options['mode'] ?? FORCE_GZIP)
            );

            if ($result === false) {
                throw new EncoderError(error_get_last()['message'] ?? 'Unknown GZip error');
            }
            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e->getMessage(), null, EncoderError::GZIP);
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
                throw new EncoderError(error_get_last()['message'] ?? 'Unknown GZip error');
            }
            return $result;
        } catch (Throwable $e) {
            $error = new EncoderError($e->getMessage(), null, EncoderError::GZIP);
            return null;
        }
    }
}
