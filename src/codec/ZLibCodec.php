<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

use froq\encoding\encoder\ZLibEncoder;
use froq\encoding\decoder\ZLibDecoder;

/**
 * ZLib codec class, provides encode/decode operations for ZLib related jobs.
 *
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\ZLibCodec
 * @author  Kerem Güneş
 * @since   6.0
 */
class ZLibCodec extends Codec
{
    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $options['encoder']['class'] = ZLibEncoder::class;
        $options['decoder']['class'] = ZLibDecoder::class;

        parent::__construct($options);
    }
}
