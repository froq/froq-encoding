<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{EncoderError, EncodingException};
use froq\dom\Dom;
use Throwable;

/**
 * Encoder.
 *
 * Represents a static encoder entity that available for JSON, XML and GZip encoding processes.
 *
 * @package froq\encoding
 * @object  froq\encoding\Encoder
 * @author  Kerem Güneş
 * @since   4.0
 * @static
 */
final class Encoder
{
    /**
     * Build a JSON string with given input.
     *
     * @param  mixed                           $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function jsonEncode(mixed $input, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            $ret = json_encode($input,
                (int) ($options['flags'] ?? 0),
                (int) ($options['depth'] ?? 512)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg());
            }

            return $ret;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

            return null;
        }
    }

    /**
     * Parse given JSON input.
     *
     * @param  string                          $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return mixed
     */
    public static function jsonDecode(string $input, array $options = null, EncoderError &$error = null): mixed
    {
        // If false given with JSON_OBJECT_AS_ARRAY in flags, simply false overrides on.
        isset($options['assoc']) && $options['assoc'] = (bool) $options['assoc'];

        try {
            $ret = json_decode($input,
                      ($options['assoc'] ?? null),
                (int) ($options['depth'] ?? 512),
                (int) ($options['flags'] ?? 0)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg());
            }

            return $ret;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

            return null;
        }
    }

    /**
     * Build a XML string with given input.
     *
     * @param  array                           $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function xmlEncode(array $input, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            return Dom::createXmlDocument($input)->toString(
                (bool)   ($options['indent']       ?? false),
                (string) ($options['indentString'] ?? '')
            );
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::XML);

            return null;
        }
    }

    /**
     * Parse given XML input.
     *
     * @param  string                          $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return array|object|null
     */
    public static function xmlDecode(string $input, array $options = null, EncoderError &$error = null): array|object|null
    {
        try {
            return Dom::parseXml($input, [
                'validateOnParse'     => (bool) ($options['validateOnParse']     ?? false),
                'preserveWhiteSpace'  => (bool) ($options['preserveWhiteSpace']  ?? false),
                'strictErrorChecking' => (bool) ($options['strictErrorChecking'] ?? false),
                'throwErrors'         => (bool) ($options['throwErrors']         ?? true),
                'flags'               => (int)  ($options['flags']               ?? 0),
                'assoc'               => (bool) ($options['assoc']               ?? true),
            ]);
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::XML);

            return null;
        }
    }

    /**
     * Encode given input using GZip utils.
     *
     * @param  string                           $in
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipEncode(string $in, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            $out = gzencode($in,
                (int) ($options['level'] ?? -1),
                (int) ($options['mode']  ?? FORCE_GZIP)
            );

            if ($out === false) {
                throw new EncoderError(error_message());
            }

            return $out;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::GZIP);

            return null;
        }
    }

    /**
     * Decode given input using GZip utils.
     *
     * @param  string                           $in
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipDecode(string $in, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            $out = gzdecode($in,
                (int) ($options['length'] ?? 0)
            );

            if ($out === false) {
                throw new EncoderError(error_message());
            }

            return $out;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::GZIP);

            return null;
        }
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
    public static function isEncoded(string $type, mixed $input): bool
    {
        return match ($type) {
            'json' => is_string($input)
                    && ($input = trim($input))
                    && isset($input[0], $input[-1])
                    && (
                        ($input[0] . $input[-1]) === '{}' ||
                        ($input[0] . $input[-1]) === '[]' ||
                        ($input[0] . $input[-1]) === '""'
                    ),
            'xml' => is_string($input)
                    && ($input = trim($input))
                    && isset($input[0], $input[-1]) && (
                        ($input[0] . $input[-1]) === '<>'
                    ),
            'gzip' => is_string($input)
                    && str_starts_with($input, "\x1f\x8b"),

            default => throw new EncodingException(
                'Invalid type `%s`, valids are: json, xml, gzip', $type
            )
        };
    }
}
