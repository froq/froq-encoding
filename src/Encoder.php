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
 * @object     Froq\Encoding\Encoder
 * @author     Kerem Güneş <k-gun@mail.com>
 * @since      3.0
 */
abstract class Encoder
{
    /**
     * Names.
     * @const string
     */
    public const NAME_JSON = 'json',
                 NAME_GZIP = 'gzip';

    /**
     * Options.
     * @var array
     */
    protected $options = [];

    /**
     * Error.
     * @var string
     */
    protected $error;

    /**
     * Constructor.
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->setOptions($options);
    }

    /**
     * Set options.
     * @param  array $options
     * @return void
     */
    public final function setOptions(array $options): void
    {
        $this->options = array_merge($this->options, $options);
    }

    /**
     * Get options.
     * @return array
     */
    public final function getOptions(): array
    {
        return $this->options;
    }

    /**
     * Has error.
     * @return bool
     */
    public final function hasError(): bool
    {
        return $this->error != null;
    }

    /**
     * Get error.
     * @return ?string
     */
    public final function getError(): ?string
    {
        return $this->error;
    }

    /**
     * Init.
     * @param  string $name
     * @param  array  $options
     * @return Froq\Encoding\EncoderInterface
     * @throws Froq\Encoding\EncoderException
     */
    public static final function init(string $name, array $options = null): EncoderInterface
    {
        switch ($name) {
            case self::NAME_JSON: return new JsonEncoder($options);
            case self::NAME_GZIP: return new GzipEncoder($options);
        }

        throw new EncoderException("Unimplemented encoder '{$name}' given");
    }

    /**
     * Json encode.
     * @param  any        $data
     * @param  array|null $options
     * @return array
     */
    public static final function jsonEncode($data, array $options = null): array
    {
        $encoder = new JsonEncoder($options);

        return [$encoder->encode($data), $encoder->hasError()
            ? new EncoderException('JSON Error: '. $encoder->getError()) : null];
    }

    /**
     * Json decode.
     * @param  any        $data
     * @param  array|null $options
     * @return array
     */
    public static final function jsonDecode($data, array $options = null): array
    {
        $encoder = new JsonEncoder($options);

        return [$encoder->decode($data), $encoder->hasError()
            ? new EncoderException('JSON Error: '. $encoder->getError()) : null];
    }

    /**
     * Gzip encode.
     * @param  ?string     $data
     * @param  array|null $options
     * @return ?string
     */
    public static final function gzipEncode($data, array $options = null): array
    {
        $encoder = new GzipEncoder($options);

        return [$encoder->encode($data), $encoder->hasError()
            ? new EncoderException('GZip Error: '. $encoder->getError()) : null];
    }

    /**
     * Gzip decode.
     * @param  ?string     $data
     * @param  array|null $options
     * @return ?string
     */
    public static final function gzipDecode($data, array $options = null): array
    {
        $encoder = new GzipEncoder($options);

        return [$encoder->decode($data), $encoder->hasError()
            ? new EncoderException('GZip Error: '. $encoder->getError()) : null];
    }
}
