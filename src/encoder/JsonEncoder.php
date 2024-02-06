<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\encoder;

/**
 * JSON encoder class, provides encode operations for JSON related jobs.
 *
 * @package froq\encoding\encoder
 * @class   froq\encoding\encoder\JsonEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonEncoder extends Encoder
{
    /** Default flags. */
    public const FLAGS = JSON_PRESERVE_ZERO_FRACTION
                       | JSON_UNESCAPED_SLASHES
                       | JSON_UNESCAPED_UNICODE;

    /** Default options. */
    protected static array $optionsDefault = [
        'flags' => 0, 'depth' => 512, 'indent' => null
    ];

    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        // Prevent pretty print corruption.
        if (isset($options['indent'])) {
            $options['flags'] ??= 0;
            $options['flags'] &= ~JSON_PRETTY_PRINT;
        }

        parent::__construct($options);
    }

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(object $object = null, mixed ...$options): bool
    {
        $this->error = null;
        $object || $this->inputCheck();

        $options = $this->options($options);

        // Wrap for type errors etc.
        try {
            // Get object variables to input if given.
            $object && $this->input = get_object_vars($object);

            $this->output = json_encode(
                $this->input,
                $options['flags'] |= static::FLAGS,
                $options['depth'],
            );

            if ($message = json_error_message($code)) {
                throw new \JsonError($message, code: $code);
            }

            // Prettify if requested.
            if ($this->output && $options['indent']) {
                $this->output = \JsonPrettifier::prettify($this->output, $options['indent']);
            }
        } catch (\Throwable $e) {
            $this->error = new EncoderError($e, code: EncoderError::JSON);
            $this->errorCheck();
        }

        return ($this->error === null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('json', $input);
    }
}
