<?php
/**
 * MIT License <https://opensource.org/licenses/mit>
 *
 * Copyright (c) 2015 Kerem Güneş
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\encoding\{AbstractEncoder, EncoderError};
use froq\dom\Dom;
use Throwable;

/**
 * Xml Encoder.
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
            $error = new EncoderError('Array needed for "%s()", "%s" given',
                [__method__, gettype($data)], EncoderError::XML);
            return null;
        }

        // Skip empty arrays.
        if ($data === []) {
            return null;
        }

        try {
            return Dom::createXmlDocument($data)->toString(
                (bool)   ($options['indent'] ?? false),
                (string) ($options['indentString'] ?? "\t")
            );
        } catch (Throwable $e) {
            $error = new EncoderError($e->getMessage(), null, EncoderError::XML);
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
            $error = new EncoderError('String data needed for "%s()", "%s" given',
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
            $error = new EncoderError($e->getMessage(), null, EncoderError::XML);
            return null;
        }
    }
}
