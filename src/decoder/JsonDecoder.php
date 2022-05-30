<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

/**
 * JSON decoder class, provides decode operations for JSON related jobs.
 *
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\JsonDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonDecoder extends Decoder
{
    /** @const int */
    public const FLAGS = JSON_BIGINT_AS_STRING;

    /** @var array */
    protected static array $optionsDefault = [
        'flags' => 0, 'depth' => 512, 'assoc' => null,
    ];

    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // If false given with JSON_OBJECT_AS_ARRAY flag, simply false overrides.
        if (isset($options['assoc'])) {
            $options['flags'] ??= 0;
            $options['flags'] &= ~JSON_OBJECT_AS_ARRAY;

            $options['assoc'] = (bool) $options['assoc'];
        }

        parent::__construct($options);
    }

    /**
     * @inheritDoc froq\encoding\decoder\Decoder
     */
    public function decode(DecoderError &$error = null, object $object = null): bool
    {
        $error = null;

        $this->inputCheck();

        // Wrap for type errors etc.
        try {
            $this->output = json_decode(
                $this->input,
                $this->options['assoc'],
                $this->options['depth'],
                $this->options['flags'] |= static::FLAGS,
            );

            if ($error = json_error_message()) {
                throw new \JsonError($error);
            }

            // Set object variables from output if given.
            $object && $this->output = set_object_vars($object, $this->output);
        } catch (\Throwable $e) {
            $error = new DecoderError(
                $e->getMessage(), code: DecoderError::JSON, cause: $e
            );

            $this->errorCheck($error);
        }

        return ($error == null);
    }
}
