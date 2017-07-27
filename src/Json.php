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
 * @object     Froq\Encoding\Json
 * @author     Kerem Güneş <k-gun@mail.com>
 */
final class Json
{
    /**
     * Data.
     * @var any
     */
    private $data;

    /**
     * Error code.
     * @var int
     */
    private $errorCode = 0;

    /**
     * Error message.
     * @var string
     */
    private $errorMessage = '';

    /**
     * Error message map.
     * @var array
     */
    private static $errorMessages = [
        JSON_ERROR_NONE           => '',
        JSON_ERROR_DEPTH          => 'Maximum stack depth exceeded',
        JSON_ERROR_STATE_MISMATCH => 'State mismatch (invalid or malformed JSON)',
        JSON_ERROR_CTRL_CHAR      => 'Unexpected control character found',
        JSON_ERROR_SYNTAX         => 'Syntax error, malformed JSON',
        JSON_ERROR_UTF8           => 'Malformed UTF-8 characters, possibly incorrectly encoded',
    ];

    /**
     * Constructor.
     * @param any $data
     */
    public function __construct($data = null)
    {
        $this->setData($data);
    }

    /**
     * Set data.
     * @param  any $data
     * @return self
     */
    public function setData($data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Get data.
     * @return any
     */
    public function getData($data)
    {
        return $this->data;
    }

    /**
     * Encode.
     * @param  ...$arguments
     * @return ?string
     */
    public function encode(...$arguments): ?string
    {
        if ($this->data === '') {
            return '';
        }

        // remove useless second arg if empty
        $arguments = array_filter($arguments);

        // add data as first arg
        array_unshift($arguments, $this->data);

        $return = call_user_func_array('json_encode', $arguments);

        $this->checkError();

        if ($return === false) {
            return null;
        }

        return $return;
    }

    /**
     * Decode.
     * @param  ...$arguments
     * @return any
     */
    public function decode(...$arguments)
    {
        if ($this->data === '') {
            return null;
        }

        // remove useless second arg if empty
        $arguments = array_filter($arguments);

        // add data as first arg
        array_unshift($arguments, $this->data);

        $return = call_user_func_array('json_decode', $arguments);

        $this->checkError();

        return $return;
    }

    /**
     * Has error.
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->errorCode > 0;
    }

    /**
     * Check error.
     * @return void
     */
    private function checkError(): void
    {
        $this->errorCode = json_last_error();
        if ($this->errorCode) {
            $this->errorMessage = isset(self::$errorMessages[$this->errorCode])
                ? self::$errorMessages[$this->errorCode]
                : 'unknown error'; // default
        }
    }

    /**
     * Get error.
     * @return array
     */
    public function getError(): array
    {
        return [
            'code' => $this->errorCode,
            'message' => $this->errorMessage,
        ];
    }

    /**
     * Get error code.
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }

    /**
     * Get error message.
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
