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

use froq\encoding\{AbstractEncoder, EncoderError, EncodingException,
    JsonEncoder, XmlEncoder, GzipEncoder};

/**
 * Encoder.
 * @package froq\encoding
 * @object  froq\encoding\Encoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Encoder
{
    /**
     * Types.
     * @const string
     */
    public const TYPE_JSON = 'json',
                 TYPE_XML  = 'xml',
                 TYPE_GZIP = 'gzip';

    /**
     * Init.
     * @param  string $type
     * @param  any    $data
     * @return froq\encoding\AbstractEncoder
     * @throws froq\encoding\EncodingException
     */
    public static function init(string $type, $data): AbstractEncoder
    {
        switch ($type) {
            case self::TYPE_JSON:
                return new JsonEncoder($data);
            case self::TYPE_XML:
                return new XmlEncoder($data);
            case self::TYPE_GZIP:
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
    public static function jsonEncode($data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_JSON, $data)->encode($options, $error);
    }

    /**
     * Json decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     */
    public static function jsonDecode(string $data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_JSON, $data)->decode($options, $error);
    }

    /**
     * Xml encode.
     * @param  array      $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function xmlEncode(array $data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_XML, $data)->encode($options, $error);
    }

    /**
     * Xml decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return array|object|null
     */
    public static function xmlDecode(string $data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_XML, $data)->decode($options, $error);
    }

    /**
     * Gzip encode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipEncode(string $data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_GZIP, $data)->encode($options, $error);
    }

    /**
     * Gzip decode.
     * @param  string $data
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipDecode(string $data, array $options = null, EncoderError &$error = null)
    {
        return self::init(self::TYPE_GZIP, $data)->decode($options, $error);
    }

    /**
     * Is encoded.
     * @param string $type
     * @param any $data
     * @return ?bool
     * @since  4.0
     */
    public static function isEncoded(string $type, $data): ?bool
    {
        if (is_string($data)) {
            switch ($type) {
                case self::TYPE_JSON:
                    return ($data = trim($data)) && isset($data[0], $data[-1]) && (
                           ($data[0] . $data[-1] == '{}')
                        || ($data[0] . $data[-1] == '[]')
                        || ($data[0] . $data[-1] == '""')
                        // Really needed?
                        // || is_numeric($data)
                        // || in_array($data, ['null', 'true', 'false'])
                    );
                case self::TYPE_XML:
                    return ($data = trim($data)) && isset($data[0], $data[-1]) && (
                        ($data[0] . $data[-1] == '<>')
                    );
                case self::TYPE_GZIP:
                    return stripos($data, "\x1f\x8b") === 0;
            }
        }

        return null; // Unknown.
    }
}
