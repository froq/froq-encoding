<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

use froq\dom\Dom;

/**
 * Xml Encoder.
 *
 * @package froq\encoding\encoder
 * @object  froq\encoding\encoder\XmlEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class XmlEncoder extends Encoder
{
    /** @var array */
    protected static array $optionsDefault = [
        'charset' => 'utf-8',
        // Encode options.
        'indent' => false, 'indentString' => "\t",
        // Decode options.
        'validateOnParse'     => false, 'preserveWhiteSpace'  => false,
        'strictErrorChecking' => false, 'throwErrors'         => true,
        'flags'               => 0,     'assoc'               => true,
    ];

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(EncoderError &$error = null): bool
    {
        $error = null;

        // Wrap for type/dom errors etc.
        try {
            $this->input = Dom::createXmlDocument(
                    $this->input,
                    $this->getOption('charset')
                )
                ->toString(
                    $this->getOptions(
                        ['indent', 'indentString'],
                        combine: true
                    )
                );
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::XML);
        }

        return ($error == null);
    }

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function decode(EncoderError &$error = null): bool
    {
        $error = null;

        // Wrap for type/dom errors etc.
        try {
            $this->input = Dom::parseXml(
                $this->input,
                $this->getOptions(
                    ['validateOnParse', 'preserveWhiteSpace', 'strictErrorChecking',
                     'throwErrors', 'flags', 'assoc'],
                    combine: true
                )
            );
        } catch (\Throwable $e) {
            $error = $this->error($e, code: EncoderError::XML);
        }

        return ($error == null);
    }
}
