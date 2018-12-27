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
 * @object     Froq\Encoding\JsonEncoder
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      3.0
 */
final class JsonEncoder extends Encoder implements EncoderInterface
{
    /**
     * Cconstructor.
     * @param  array $options
     * @throws Froq\Encoding\EncoderException
     */
    public function __construct(array $options = [])
    {
        if (!function_exists('json_encode')) {
            throw new EncoderException('JSON module not found!');
        }

        // set defaults
        $options = [
            'flags' => (int) ($options['flags'] ?? 0),
            'depth' => (int) ($options['depth'] ?? 512),
            'assoc' => (bool) ($options['assoc'] ?? false)
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
        // skip empty strings
        if ($data === '') {
            return '""';
        }

        $data = json_encode($data, $this->options['flags'], $this->options['depth']);
        if (json_last_error() > 0) {
            $data = null;
            $this->error = json_last_error_msg() ?: 'Unknown error';
        }

        return $data;
    }

    /**
     * Decode.
     * @param  ?string $data
     * @return ?any
     */
    public function decode($data)
    {
        // skip empty strings (that cause "Syntax error"?)
        if ($data === '') {
            return null;
        }

        $data = json_decode((string) $data, $this->options['assoc'], $this->options['depth'],
            $this->options['flags']);
        if (json_last_error() > 0) {
            $data = null;
            $this->error = json_last_error_msg() ?: 'Unknown error';
        }

        return $data;
    }
}
