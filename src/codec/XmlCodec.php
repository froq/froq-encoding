<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

use froq\encoding\encoder\XmlEncoder;
use froq\encoding\decoder\XmlDecoder;

/**
 * XML codec class, provides encode/decode operations for XML related jobs.
 *
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\XmlCodec
 * @author  Kerem Güneş
 * @since   6.0
 */
class XmlCodec extends Codec
{
    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $options['encoder']['class'] = XmlEncoder::class;
        $options['decoder']['class'] = XmlDecoder::class;

        parent::__construct($options);
    }
}
