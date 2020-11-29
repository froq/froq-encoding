<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{Encoder, EncoderError};

/**
 * Abstract Encoder.
 *
 * @package froq\encoding
 * @object  froq\encoding\AbstractEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   3.0, 4.0 Refactored.
 */
abstract class AbstractEncoder
{
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
        return Encoder::isEncoded($this->type, $this->data);
    }

    /**
     * Encode.
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     * @since  4.0 Moved from encoder interface.
     */
    abstract public function encode(array $options = null, EncoderError &$error = null);

    /**
     * Decode.
     * @param  array|null $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     * @since  4.0 Moved from encoder interface.
     */
    abstract public function decode(array $options = null, EncoderError &$error = null);
}
