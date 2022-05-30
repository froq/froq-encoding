<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

/**
 * JSON encoder class, provides encode operations for JSON related jobs.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\JsonEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonEncoder extends Encoder
{
    /** @const int */
    public const FLAGS = JSON_PRESERVE_ZERO_FRACTION
                       | JSON_UNESCAPED_SLASHES
                       | JSON_UNESCAPED_UNICODE;

    /** @var array */
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
    public function encode(EncoderError &$error = null, object $object = null): bool
    {
        $error = null;

        $object || $this->inputCheck();

        // Wrap for type errors etc.
        try {
            // Get object variables to input if given.
            $object && $this->input = get_object_vars($object);

            $this->output = json_encode(
                $this->input,
                $this->options['flags'] |= static::FLAGS,
                $this->options['depth'],
            );

            if ($error = json_error_message()) {
                throw new \JsonError($error);
            }

            // Prettify if requested.
            if ($this->options['indent']) {
                $this->output = self::prettify($this->output, $this->options['indent']);
            }
        } catch (\Throwable $e) {
            $error = new EncoderError(
                $e->getMessage(), code: EncoderError::JSON, cause: $e
            );

            $this->errorCheck($error);
        }

        return ($error == null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('json', $input);
    }

    /**
     * Pretty print with indent option.
     *
     * @param  string     $json
     * @param  string|int $indent
     * @param  string     $newLine
     * @return string
     * @throws froq\encoding\encoder\EncoderException
     * @thanks https://github.com/ergebnis/json-printer
     */
    public static function prettify(string $json, string|int $indent = "  ", string $newLine = "\n"): string
    {
        // When no indent available.
        if (!$json || !strpbrk($json, '{[')) {
            return $json;
        }

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
