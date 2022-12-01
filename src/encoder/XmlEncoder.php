<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\encoder;

use froq\dom\Dom;

/**
 * XML encoder class, provides encode operations for XML related jobs.
 *
 * @package froq\encoding\encoder
 * @class   froq\encoding\encoder\XmlEncoder
 * @author  Kerem Güneş
 * @since   6.0
 */
class XmlEncoder extends Encoder
{
    /** Default options. */
    protected static array $optionsDefault = [
        'charset' => 'utf-8', 'indent' => false, 'indentString' => '  ',
    ];

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(mixed ...$options): bool
    {
        $this->error = null;
        $this->inputCheck();

        $options = $this->options($options, [
            'charset', 'indent', 'indentString'
        ]);

        // Wrap for type/dom errors etc.
        try {
            $this->output = Dom::createXmlDocument(
                    $this->input,
                    $options['charset']
                )->toString([
                    'indent'       => $options['indent'],
                    'indentString' => $options['indentString']
                ]);
        } catch (\Throwable $e) {
            $this->error = new EncoderError($e, code: EncoderError::XML);
            $this->errorCheck();
        }

        return ($this->error === null);
    }

    /**
     * @override
     */
    public static function isEncoded(mixed $input, mixed $_ = null): bool
    {
        return parent::isEncoded('xml', $input);
    }
}
