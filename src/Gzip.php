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
 * @object     Froq\Encoding\Gzip
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Gzip
{
    /**
     * Default level.
     * @const int
     */
    public const DEFAULT_LEVEL = -1;

    /**
     * Default mode.
     * @const int
     */
    public const DEFAULT_MODE  = FORCE_GZIP;

    /**
     * Data.
     * @var string
     */
    private $data;

    /**
     * Data minlen.
     * @var int
     */
    private $dataMinlen = 1024;

    /**
     * Level.
     * @var int
     */
    private $level;

    /**
     * Mode.
     * @var int
     */
    private $mode;

    /**
     * Is encoded.
     * @var bool
     */
    private $isEncoded = false;

    /**
     * Constructor.
     * @param string $data
     * @param int    $level
     * @param int    $mode
     */
    public function __construct(string $data = null, int $level = self::DEFAULT_LEVEL,
        int $mode = self::DEFAULT_MODE)
    {
        if (!function_exists('gzencode')) {
            throw new GzipException('GZip module not found!');
        }

        $this->setData($data)->setLevel($level)->setMode($mode);
    }

    /**
     * Set data.
     * @param string $data
     */
    public function setData(string $data = null): self
    {
        $this->data = (string) $data;

        return $this;
    }

    /**
     * Get data.
     * @return string
     */
    public function getData(): string
    {
        return $this->data;
    }

    /**
     * Set data minlen.
     * @param int $dataMinlen
     */
    public function setDataMinlen(int $dataMinlen): self
    {
        $this->dataMinlen = $dataMinlen;

        return $this;
    }

    /**
     * Get data minlen.
     * @return int
     */
    public function getDataMinlen(): int
    {
        return $this->dataMinlen;
    }

    /**
     * Set level.
     * @param int $level
     */
    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level.
     * @return int
     */
    public function getLevel(): int
    {
        return $this->level;
    }

    /**
     * Set mode.
     * @param int $mode
     */
    public function setMode(int $mode): self
    {
        $this->mode = $mode;

        return $this;
    }

    /**
     * Get mode.
     * @return int
     */
    public function getMode(): int
    {
        return $this->mode;
    }

    /**
     * Encode.
     * @return string
     */
    public function encode(): string
    {
        if (!$this->isEncoded) {
            $this->isEncoded = true;
            $this->data = gzencode($this->data, $this->level, $this->mode);
        }

        return $this->data;
    }

    /**
     * Decode.
     * @return string
     */
    public function decode(): string
    {
        if ($this->isEncoded) {
            $this->isEncoded = false;
            $this->data = gzdecode($this->data);
        }

        return $this->data;
    }

    /**
     * Check encoded.
     * @return bool
     */
    public function isEncoded(): bool
    {
        return $this->isEncoded;
    }

    /**
     * Check minlen.
     * @return bool
     */
    public function checkDataMinlen(): bool
    {
        return strlen($this->data) >= $this->dataMinlen;
    }
}
