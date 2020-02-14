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

use froq\encoding\{Encoder, EncoderInterface, EncoderError, EncodingException,
    JsonEncoder, XmlEncoder, GzipEncoder};

/**
 * Util.
 * @package froq\encoding
 * @object  froq\encoding\Util
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Util
{
    /**
     * Init encoder.
     * @param  string $type
     * @param  any    $data
     * @return froq\encoding\EncoderInterface
     * @throws froq\encoding\EncodingException If no valid encoder type given.
     */
    public static final function initEncoder(string $type, $data): EncoderInterface
    {
        switch ($type) {
            case Encoder::TYPE_JSON:
                return new JsonEncoder($data);
            case Encoder::TYPE_XML:
                return new xmlEncoder($data);
            case Encoder::TYPE_GZIP:
                return new GzipEncoder($data);
        }

        throw new EncodingException('Unimplemented encoder type "%s"', [$type]);
    }

    /**
     * Json encode.
     * @param  any $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static final function jsonEncode($data, array $options = null,
        EncoderError &$error = null)
    {
        return (new JsonEncoder($data))->encode($options, $error);
    }

    /**
     * Json decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     */
    public static final function jsonDecode(string $data, array $options = null,
        EncoderError &$error = null)
    {
        return (new JsonEncoder($data))->decode($options, $error);
    }

    /**
     * Xml encode.
     * @param  array|object $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static final function xmlEncode($data, array $options = null,
        EncoderError &$error = null)
    {
        return (new XmlEncoder($data))->encode($options, $error);
    }

    /**
     * Xml decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return array|object|null
     */
    public static final function xmlDecode(string $data, array $options = null,
        EncoderError &$error = null)
    {
        return (new XmlEncoder($data))->decode($options, $error);
    }

    /**
     * Gzip encode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static final function gzipEncode(string $data, array $options = null,
        EncoderError &$error = null)
    {
        return (new GzipEncoder($data))->encode($options, $error);
    }

    /**
     * Gzip decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static final function gzipDecode(string $data, array $options = null,
        EncoderError &$error = null)
    {
        return (new GzipEncoder($data))->decode($options, $error);
    }

    /**
     * Is encoded.
     * @param string $type
     * @param any $data
     * @return ?bool
     * @since  4.0
     */
    public static final function isEncoded(string $type, $data): ?bool
    {
        switch ($type) {
            case Encoder::TYPE_JSON:
                return is_string($data) && isset($data[0], $data[-1]) && (
                       ($data[0] . $data[-1] == '{}')
                    || ($data[0] . $data[-1] == '[]')
                    || ($data[0] . $data[-1] == '""')
                );
            case Encoder::TYPE_XML:
                return is_string($data) && isset($data[0], $data[-1]) && (
                    ($data[0] . $data[-1] == '<>')
                );
            case Encoder::TYPE_GZIP:
                return is_string($data) && (strpos($data, "\x1F\x8B") === 0);
        }

        return null; // Unknown.
    }
}
