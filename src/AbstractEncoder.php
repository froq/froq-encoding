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

use froq\encoding\{EncoderError, Util as EncodingUtil};

/**
 * Abstract Encoder.
 * @package froq\encoding
 * @object  froq\encoding\AbstractEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0, 4.0 Refactored.
 */
abstract class AbstractEncoder
{
    /**
     * Types.
     * @const string
     */
    public const TYPE_JSON = 'json',
                 TYPE_XML  = 'xml',
                 TYPE_GZIP = 'gzip';

    /**
     * Type.
     * @var   string
     * @since 4.0
     */
    protected string $type;

    /**
     * Data.
     * @var   any
     * @since 4.0
     */
    protected $data;

    /**
     * Constructor.
     * @param string $type
     * @param any    $data
     */
    public function __construct(string $type, $data)
    {
        $this->type = $type;
        $this->data = $data;
    }

    /**
     * Type.
     * @return string
     * @since  4.0
     */
    public final function type(): string
    {
        return $this->type;
    }

    /**
     * Data.
     * @return any
     * @since  4.0
     */
    public final function data()
    {
        return $this->data;
    }

    /**
     * Is encoded.
     * @return bool
     * @since  4.0
     */
    public final function isEncoded(): bool
    {
        return EncodingUtil::isEncoded($this->type, $this->data);
    }

    /**
     * Encode.
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     * @since 4.0 Moved from encoder interface.
     */
    abstract public function encode(array $options = null, EncoderError &$error = null);

    /**
     * Decode.
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     * @since 4.0 Moved from encoder interface.
     */
    abstract public function decode(array $options = null, EncoderError &$error = null);
}
