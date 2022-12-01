<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

use froq\encoding\encoder\GZipEncoder;
use froq\encoding\decoder\GZipDecoder;

/**
 *  GZip codec class, provides encode/decode operations for GZip related jobs.
 *
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\GZipCodec
 * @author  Kerem Güneş
 * @since   6.0
 */
class GZipCodec extends Codec
{
    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $options['encoder']['class'] = GZipEncoder::class;
        $options['decoder']['class'] = GZipDecoder::class;

        parent::__construct($options);
    }
}
