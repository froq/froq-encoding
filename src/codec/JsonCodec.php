<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding\codec;

use froq\encoding\encoder\JsonEncoder;
use froq\encoding\decoder\JsonDecoder;

/**
 * JSON codec class, provides encode/decode operations for JSON related jobs.
 *
 * @package froq\encoding\codec
 * @class   froq\encoding\codec\JsonCodec
 * @author  Kerem Güneş
 * @since   6.0
 */
class JsonCodec extends Codec
{
    /**
     * Constructor.
     *
     * @param array|null $options
     */
    public function __construct(array $options = null)
    {
        $options['encoder']['class'] = JsonEncoder::class;
        $options['decoder']['class'] = JsonDecoder::class;

        parent::__construct($options);
    }
}
