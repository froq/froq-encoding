<?php
/**
 * Copyright (c) 2016 Kerem Güneş
 *     <k-gun@mail.com>
 *
 * GNU General Public License v3.0
 *     <http://www.gnu.org/licenses/gpl-3.0.txt>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
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
    const DEFAULT_LEVEL = -1;

    /**
     * Default mode.
     * @const int
     */
    const DEFAULT_MODE  = FORCE_GZIP;

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
