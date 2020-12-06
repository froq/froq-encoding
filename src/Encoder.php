<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
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
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 * @static
 */
final class Encoder
{
    /**
     * Build a JSON string with given input.
     *
     * @param  any                              $in
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function jsonEncode($in, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            $out = json_encode($in,
                (int) ($options['flags'] ?? 0),
                (int) ($options['depth'] ?? 512)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg());
            }

            return $out;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

            return null;
        }
    }

    /**
     * Parse given JSON input.
     *
     * @param  string                           $in
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return any
     */
    public static function jsonDecode(string $in, array $options = null, EncoderError &$error = null)
    {
        // If false given with JSON_OBJECT_AS_ARRAY in flags, simply false overrides on.
        isset($options['assoc']) && $options['assoc'] = (bool) $options['assoc'];

        try {
            $out = json_decode($in,
                       $options['assoc'] ?? null,
                (int) ($options['depth'] ?? 512),
                (int) ($options['flags'] ?? 0)
            );

            if (json_last_error()) {
                throw new EncoderError(json_last_error_msg());
            }

            return $out;
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::JSON);

            return null;
        }
    }

    /**
     * Build a XML string with given input.
     *
     * @param  array                            $in
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function xmlEncode(array $in, array $options = null, EncoderError &$error = null): string|null
    {
        try {
            return Dom::createXmlDocument($in)->toString(
                (bool)   ($options['indent'] ?? false),
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
     * @param  string                           $data
     * @param  array|null                       $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return array|object|null
     */
    public static function xmlDecode(string $data, array $options = null, EncoderError &$error = null): array|object|null
    {
        try {
            return Dom::parseXml($data, [
                'validateOnParse'     => (bool) ($options['validateOnParse'] ?? false),
                'preserveWhiteSpace'  => (bool) ($options['preserveWhiteSpace'] ?? false),
                'strictErrorChecking' => (bool) ($options['strictErrorChecking'] ?? false),
                'throwErrors'         => (bool) ($options['throwErrors'] ?? true),
                'flags'               => (int)  ($options['flags'] ?? 0),
                'assoc'               => (bool) ($options['assoc'] ?? true),
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
                (int) ($options['mode'] ?? FORCE_GZIP)
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
     * @param  any    $data
     * @return bool
     * @since  4.0
     * @throws froq\encoding\EncodingException
     */
    public static function isEncoded(string $type, $data): bool
    {
        return match ($type) {
            'json' => is_string($data)
                    && ($data = trim($data))
                    && isset($data[0], $data[-1])
                    && (
                        ($data[0] . $data[-1]) === '{}' ||
                        ($data[0] . $data[-1]) === '[]' ||
                        ($data[0] . $data[-1]) === '""'
                    ),
            'xml' => is_string($data)
                    && ($data = trim($data))
                    && isset($data[0], $data[-1]) && (
                        ($data[0] . $data[-1]) === '<>'
                    ),
            'gzip' => is_string($data)
                    && strpos($data, "\x1f\x8b") === 0,

            default => throw new EncodingException(
                "Invalid type '%s', valids are: json, xml, gzip", $type
            )
        };
    }
}
