<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

use froq\common\trait\OptionTrait;

/**
 * Encoder.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\Encoder
 * @author  Kerem Güneş
 * @since   4.0, 6.0
 */
abstract class Encoder
{
    use OptionTrait;

    /** @const string */
    public final const TYPE_GZIP = 'gzip',
                       TYPE_JSON = 'json',
                       TYPE_XML  = 'xml';

    /** @var mixed */
    protected mixed $input;

    /**
     * Constructor.
     *
     * @param ?array      $options
     * @param ?array|null $optionsDefault
     */
    public function __construct(?array $options, ?array $optionsDefault = null)
    {
        // When defined in subclass.
        $optionsDefault ??= static::$optionsDefault ?? null;

        // Select string keys only (map=true).
        $options = array_options($options, $optionsDefault, map: true);

        $this->setOptions($options);
    }

    /**
     * Set input.
     *
     * @param  mixed $input
     * @return self
     */
    public function setInput(mixed $input): self
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input.
     *
     * @return mixed
     */
    public function getInput(): mixed
    {
        return $this->input;
    }

    /**
     * Flush input data.
     *
     * @return void
     */
    public function flush(): void
    {
        $this->input = null;
    }

    /**
     * Create an error.
     *
     * @param  Throwable $e
     * @param  int|null  $code
     * @return froq\encoding\encoder\EncoderError
     */
    public final function error(\Throwable $e, int $code = null): EncoderError
    {
        return new EncoderError($e, code: $code ?? $e->getCode());
    }

    /**
     * Create an encoder instance.
     *
     * @param  string     $type
     * @param  array|null $options
     * @return froq\encoding\encoder\Encoder
     */
    public static final function create(string $type, array $options = null): Encoder
    {
        return match ($type) {
            self::TYPE_GZIP => new GzipEncoder($options),
            self::TYPE_JSON => new JsonEncoder($options),
            self::TYPE_XML  => new XmlEncoder($options),

            // Not implemented.
            default => throw new EncoderException(
                'Invalid type `%s` [valids: gzip, json, xml]', $type
            )
        };
    }

    /**
     * Check encoded status of given input.
     *
     * @param  string $type
     * @param  mixed  $input
     * @return bool
     * @since  4.0
     * @throws froq\encoding\EncodingException
     */
    public static final function isEncoded(string $type, mixed $input): bool
    {
        return match ($type) {
            'json' => is_string($input)
                   && isset($input[0], $input[-1])
                   && (
                        ($input[0] . $input[-1]) === '{}' ||
                        ($input[0] . $input[-1]) === '[]' ||
                        ($input[0] . $input[-1]) === '""'
                      ),
            'xml' => is_string($input)
                  && isset($input[0], $input[-1])
                  && (
                        ($input[0] . $input[-1]) === '<>'
                     ),
            'gzip' => is_string($input)
                   && str_starts_with($input, "\x1F\x8B"),

            default => throw new EncodingException(
                'Invalid type `%s` [valids: gzip, json, xml]', $type
            )
        };
    }

    /**
     * Encode.
     *
     * @param  froq\encoding\encoder\EncoderError|null &$error
     * @return bool
     */
    abstract public function encode(EncoderError &$error = null): bool;

    /**
     * Decode.
     *
     * @param  froq\encoding\encoder\EncoderError|null &$error
     * @return bool
     */
    abstract public function decode(EncoderError &$error = null): bool;
}
