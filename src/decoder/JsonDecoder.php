<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

/**
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\JsonDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonDecoder extends Decoder
{
    /** @const int */
    public final const FLAGS = JSON_BIGINT_AS_STRING;

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
        $this->ensureInput();

        $error = null;

        // Wrap for type errors etc.
        try {
            $this->input = json_decode(
                $this->input,
                $this->options['assoc'],
                $this->options['depth'],
                $this->options['flags'] |= self::FLAGS,
            );

            if ($error = json_error_message()) {
                throw new \LastError($error);
            }

            // Set object variables from input if given.
            $object && $this->input = set_object_vars($object, $this->input);
        } catch (\Throwable $e) {
            $error = new DecoderError(
                $e->getMessage(), code: DecoderError::JSON, cause: $e
            );
        }

        return ($error == null);
    }
}
