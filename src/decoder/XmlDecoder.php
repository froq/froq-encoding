<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\decoder;

use froq\dom\Dom;

/**
 * XML decoder class, provides decode operations for XML related jobs.
 *
 * @package froq\encoding\decoder
 * @class   froq\encoding\decoder\XmlDecoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class XmlDecoder extends Decoder
{
    /** Default options. */
    protected static array $optionsDefault = [
        'validateOnParse'     => false, 'preserveWhiteSpace' => false,
        'strictErrorChecking' => false, 'throwErrors'        => true,
        'flags'               => 0,     'assoc'              => true,
    ];

    /**
     * @inheritDoc froq\encoding\decoder\Decoder
     */
    public function decode(mixed ...$options): bool
    {
        $this->error = null;
        $this->inputCheck();

        $options = $this->options($options, [
            'validateOnParse', 'preserveWhiteSpace', 'strictErrorChecking',
            'throwErrors',     'flags',              'assoc'
        ]);

        // Wrap for type/dom errors etc.
        try {
            $this->output = Dom::parseXml(
                $this->input,
                $options
            );
        } catch (\Throwable $e) {
            $this->error = new DecoderError($e, code: DecoderError::XML);
            $this->errorCheck();
        }

        return ($this->error === null);
    }
}
