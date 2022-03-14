<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
 * Json Encoder.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\JsonEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonEncoder extends Encoder
{
    /**
     * Default encode flags.
     * @const int
     */
    public final const ENCODE_FLAGS = JSON_PRESERVE_ZERO_FRACTION
                                    | JSON_UNESCAPED_SLASHES
                                    | JSON_UNESCAPED_UNICODE;

    /**
     * Default decode flags.
     * @const int
     */
    public final const DECODE_FLAGS = JSON_BIGINT_AS_STRING;

    /** @var array */
    protected static array $optionsDefault = [
        'flags' => 0,    'depth'  => 512,
        'assoc' => null, 'indent' => null,
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
            $options['assoc'] = (bool) $options['assoc'];
        }

        // Pevent pretty print corruption.
        if (isset($options['indent'])) {
            $options['flags'] ??= 0;
            $options['flags'] &= ~JSON_PRETTY_PRINT;
        }

        parent::__construct($options);
    }

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(EncoderError &$error = null, object $object = null): bool
    {
        $error = null;

        // Wrap for type errors etc.
        try {
            // Get object variables to input if given.
            $object && $this->input = get_object_vars($object);

            $input = json_encode(
                $this->input,
                $this->options['flags'] |= self::ENCODE_FLAGS,
                $this->options['depth'],
            );

            if (json_last_error()) {
                throw new \Error(json_last_error_msg());
            }

            // Prettify if requested.
            if ($this->options['indent']) {
                $input = self::prettify($input, $this->options['indent']);
            }

            $this->input = $input;
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::JSON);
        }

        return ($error == null);
    }

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function decode(EncoderError &$error = null, object $object = null): bool
    {
        $error = null;

        // Wrap for type errors etc.
        try {
            $input = json_decode(
                $this->input,
                $this->options['assoc'],
                $this->options['depth'],
                $this->options['flags'] |= self::DECODE_FLAGS,
            );

            if (json_last_error()) {
                throw new \Error(json_last_error_msg());
            }

            $this->input = $input;

            // Set object variables from input if given.
            $object && set_object_vars($object, $this->input);
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::JSON);
        }

        return ($error == null);
    }

    /**
     * Pretty print with indent option.
     *
     * @param  string     $json
     * @param  int|string $indent
     * @param  string     $newLine
     * @return string
     * @throws froq\encoding\encoder\EncoderException
     * @thanks https://github.com/ergebnis/json-printer
     */
    public static function prettify(string $json, string|int $indent = "  ", string $newLine = "\n"): string
    {
        // When indent count given (@permissive-type).
        is_numeric($indent) && $indent = str_repeat(' ', (int) $indent);

        if (!preg_test('~^( +|\t+)$~', $indent)) {
            throw new EncoderException('Invalid indent `%s`', $indent);
        }
        if (!preg_test('~^(?>\r\n|\n|\r)$~', $newLine)) {
            throw new EncoderException('Invalid new-line `%s`', $newLine);
        }

        $indentLevel = 0;
        $indentString = $indent;

        // Indentation macro, makes auto-indent by level.
        $indent = function () use ($indentString, &$indentLevel) {
            return str_repeat($indentString, $indentLevel);
        };

        // Loop variables.
        $noEscape = true;
        $stringLiteral = '';
        $inStringLiteral = false;

        // Formatted string.
        $buffer = '';

        for ($i = 0, $il = strlen($json); $i < $il; $i++) {
            $char = $json[$i];

            // Are we inside a quoted string literal?
            if ($noEscape && $char == '"') {
                $inStringLiteral = !$inStringLiteral;
            }

            // Collect characters if we are inside a quoted string literal.
            if ($inStringLiteral) {
                $stringLiteral .= $char;
                $noEscape = ($char == '\\') ? !$noEscape : true;
                continue;
            }

            // Process string literal if we are about to leave it.
            if ($stringLiteral != '') {
                $buffer .= $stringLiteral . $char;
                $stringLiteral = '';
                continue;
            }

            // Ignore whitespace outside of string literal.
            if ($char == ' ') {
                continue;
            }

            // Ensure space after ":" character.
            if ($char == ':') {
                $buffer .= ': ';
                continue;
            }

            // Output a new line after "," character and and indent the next line.
            if ($char == ',') {
                $buffer .= $char . $newLine . $indent();
                continue;
            }

            // Output a new line after "{" and "[" and indent the next line.
            if ($char == '{' || $char == '[') {
                $indentLevel++;

                $buffer .= $char . $newLine . $indent();
                continue;
            }

            // Output a new line after "}" and "]" and indent the next line.
            if ($char == '}' || $char == ']') {
                $indentLevel--;

                $temp = rtrim($buffer);
                $last = $temp[-1];

                // Collapse empty {} and [].
                if ($last == '{' || $last == '[') {
                    $buffer = $temp . $char;
                    continue;
                }

                $buffer .= $newLine . $indent();
            }

            $buffer .= $char;
        }

        return $buffer;
    }
}
