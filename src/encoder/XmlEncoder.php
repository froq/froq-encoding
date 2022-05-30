<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\encoder;

use froq\dom\Dom;

/**
 * XML encoder class, provides encode operations for XML related jobs.
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
        'charset' => 'utf-8', 'indent' => false, 'indentString' => '  ',
    ];

    /**
     * @inheritDoc froq\encoding\encoder\Encoder
     */
    public function encode(EncoderError &$error = null): bool
    {
        $error = null;

        $this->inputCheck();

        // Wrap for type/dom errors etc.
        try {
            $this->output = Dom::createXmlDocument(
                    $this->input,
                    $this->getOption('charset')
                )->toString(
                    $this->getOptions(
                        ['indent', 'indentString'],
                        combine: true
                    )
                );
        } catch (\Throwable $e) {
            $error = new EncoderError(
                $e->getMessage(), code: EncoderError::XML, cause: $e
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
        return parent::isEncoded('xml', $input);
    }
}
