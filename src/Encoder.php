<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\dom\Dom;

/**
 * Encoder.
 *
 * A static encoder class for JSON, XML and GZip encoding/decoding processes.
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
     * JSON encode.
     *
     * @param  mixed                           $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function jsonEncode(mixed $input, array $options = null, EncoderError &$error = null): string|null
    {
        $error = null;
        try {
            $ret = json_encode($input,
                (int) ($options['flags'] ?? 0),
                (int) ($options['depth'] ?? 512)
            );

            if (json_last_error()) {
                throw new \Error(json_last_error_msg());
            }

            return $ret;
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::JSON);

            return null;
        }
    }

    /**
     * JSON decode.
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

        $error = null;
        try {
            $ret = json_decode($input,
                      ($options['assoc'] ?? null),
                (int) ($options['depth'] ?? 512),
                (int) ($options['flags'] ?? 0)
            );

            if (json_last_error()) {
                throw new \Error(json_last_error_msg());
            }

            return $ret;
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::JSON);

            return null;
        }
    }

    /**
     * XML encode.
     *
     * @param  array                           $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function xmlEncode(array $input, array $options = null, EncoderError &$error = null): string|null
    {
        $error = null;
        try {
            return Dom::createXmlDocument($input)->toString(
                (bool)   ($options['indent']       ?? false),
                (string) ($options['indentString'] ?? '')
            );
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::XML, cause: $e);

            return null;
        }
    }

    /**
     * XML decode.
     *
     * @param  string                          $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return array|object|null
     */
    public static function xmlDecode(string $input, array $options = null, EncoderError &$error = null): array|object|null
    {
        $error = null;
        try {
            return Dom::parseXml($input, [
                'validateOnParse'     => (bool) ($options['validateOnParse']     ?? false),
                'preserveWhiteSpace'  => (bool) ($options['preserveWhiteSpace']  ?? false),
                'strictErrorChecking' => (bool) ($options['strictErrorChecking'] ?? false),
                'throwErrors'         => (bool) ($options['throwErrors']         ?? true),
                'flags'               => (int)  ($options['flags']               ?? 0),
                'assoc'               => (bool) ($options['assoc']               ?? true),
            ]);
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::XML, cause: $e);

            return null;
        }
    }

    /**
     * GZip encode.
     *
     * @param  string                          $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipEncode(string $input, array $options = null, EncoderError &$error = null): string|null
    {
        $error = null;
        try {
            $ret = gzencode($input,
                (int) ($options['level'] ?? -1),
                (int) ($options['mode']  ?? FORCE_GZIP)
            );

            if ($ret === false) {
                throw new \Error(error_message());
            }

            return $ret;
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::GZIP);

            return null;
        }
    }

    /**
     * GZip decode.
     *
     * @param  string                          $input
     * @param  array|null                      $options
     * @param  froq\encoding\EncoderError|null &$error
     * @return string|null
     */
    public static function gzipDecode(string $input, array $options = null, EncoderError &$error = null): string|null
    {
        $error = null;
        try {
            $ret = gzdecode($input,
                (int) ($options['length'] ?? 0)
            );

            if ($ret === false) {
                throw new \Error(error_message());
            }

            return $ret;
        } catch (\Throwable $e) {
            $error = new EncoderError($e->getMessage(), code: EncoderError::GZIP);

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
                    && str_starts_with($input, "\x1F\x8B"),

            default => throw new EncodingException(
                'Invalid type `%s` [valids: json, xml, gzip]',
                $type
            )
        };
    }
}
