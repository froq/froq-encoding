<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{AbstractEncoder, EncoderError};
use froq\dom\Dom;
use Throwable;

/**
 * Xml Encoder.
 *
 * @package froq\encoding
 * @object  froq\encoding\XmlEncoder
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 */
final class XmlEncoder extends AbstractEncoder
{
    /**
     * Constructor.
     * @param array|string $data
     */
    public function __construct($data)
    {
        parent::__construct(Encoder::TYPE_XML, $data);
    }

    /**
     * @inheritDoc froq\encoding\AbstractEncoder
     */
    public function encode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        if (!is_array($data)) {
            $error = new EncoderError("Array needed for '%s()', '%s' given",
                [__method__, gettype($data)], EncoderError::XML);
            return null;
        }

        // Skip empty arrays.
        if ($data === []) {
            return '';
        }

        try {
            return Dom::createXmlDocument($data)->toString(
                (bool)   ($options['indent'] ?? false),
                (string) ($options['indentString'] ?? "\t")
            );
        } catch (Throwable $e) {
            $error = new EncoderError($e, null, EncoderError::XML);
            return null;
        }
    }

    /**
     * @inheritDoc froq\encoding\AbstractEncoder
     */
    public function decode(array $options = null, EncoderError &$error = null)
    {
        $data = $this->data;

        if (!is_string($data)) {
            $error = new EncoderError("String data needed for '%s()', '%s' given",
                [__method__, gettype($data)], EncoderError::XML);
            return null;
        }

        // Skip empty strings.
        if ($data === '') {
            return null;
        }

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
}
