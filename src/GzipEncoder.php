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

namespace Froq\Encoding;

/**
 * @package    Froq
 * @subpackage Froq\Encoding
 * @object     Froq\Encoding\GzipEncoder
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      3.0
 */
final class GzipEncoder extends Encoder implements EncoderInterface
{
    /**
     * Cconstructor.
     * @param  array $options
     * @throws Froq\Encoding\EncoderException
     */
    public function __construct(array $options = [])
    {
        if (!function_exists('gzencode')) {
            throw new EncoderException('GZip module not found');
        }

        // set defaults
        $options = [
            'level' => (int) ($options['level'] ?? -1),
            'mode' => (int) ($options['mode'] ?? FORCE_GZIP),
            'length' => (int) ($options['length'] ?? PHP_INT_MAX)
        ];

        parent::__construct($options);
    }

    /**
     * Encode.
     * @param  ?string $data
     * @return ?string
     */
    public function encode($data)
    {
        if ($data !== null) {
            $data = gzencode($data, $this->options['level'], $this->options['mode']);
            if ($data === false) { // error?
                $data = null;
                $this->error = error_get_last()['message'] ?? 'Unknown';
            }
        }

        return $data;
    }

    /**
     * Decode.
     * @param  ?string $data
     * @return ?string
     */
    public function decode($data)
    {
        if ($data !== null) {
            $data = gzdecode($data, $this->options['length']);
            if ($data === false) { // error?
                $data = null;
                $this->error = error_get_last()['message'] ?? 'Unknown';
            }
        }

        return $data;
    }
}
