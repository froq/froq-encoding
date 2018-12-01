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
    private $errorCode;

    /**
     * Error message.
     * @var string
     */
    private $errorMessage;

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
        // skip empty strings
        if ($this->data === '') {
            return '""';
        }

        // remove useless second argument if empty
        $arguments = array_filter($arguments);

        // add data as first argument
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
        // skip empty strings that cause "Syntax error"
        if ($this->data === '') {
            return null;
        }

        // remove useless second argument if empty
        $arguments = array_filter($arguments);

        // add data as first argument
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
     * Get error.
     * @return array
     */
    public function getError(): array
    {
        return ['code' => $this->errorCode, 'message' => $this->errorMessage];
    }

    /**
     * Get error code.
     * @return ?int
     */
    public function getErrorCode(): ?int
    {
        return $this->errorCode;
    }

    /**
     * Get error message.
     * @return ?string
     */
    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    /**
     * Check error.
     * @return void
     */
    private function checkError(): void
    {
        $errorCode = json_last_error();
        if ($errorCode > 0) {
            $this->errorCode = $errorCode;
            $this->errorMessage = json_last_error_msg() ?: 'Unknown error';
        }
    }
}
