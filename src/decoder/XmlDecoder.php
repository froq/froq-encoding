<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\decoder;

use froq\dom\Dom;

/**
 * @package froq\encoding\decoder
 * @object  froq\encoding\decoder\XmlDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class XmlDecoder extends Decoder
{
    /** @var array */
    protected static array $optionsDefault = [
        'validateOnParse'     => false, 'preserveWhiteSpace' => false,
        'strictErrorChecking' => false, 'throwErrors'        => true,
        'flags'               => 0,     'assoc'              => true,
    ];

    /**
     * @inheritDoc froq\encoding\decoder\Decoder
     */
    public function decode(DecoderError &$error = null): bool
    {
        $this->ensureInput();

        $error = null;

        // Wrap for type/dom errors etc.
        try {
            $this->output = Dom::parseXml(
                $this->input,
                $this->getOptions(
                    ['validateOnParse', 'preserveWhiteSpace', 'strictErrorChecking',
                     'throwErrors', 'flags', 'assoc'],
                    combine: true
                )
            );
        } catch (\Throwable $e) {
            $error = new DecoderError(
                $e->getMessage(), code: DecoderError::XML, cause: $e
            );
        }

        return ($error == null);
    }
}
