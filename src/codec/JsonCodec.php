<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding\codec;

use froq\encoding\encoder\JsonEncoder;
use froq\encoding\decoder\JsonDecoder;

/**
 * @package froq\encoding\codec
 * @object  froq\encoding\codec\JsonCodec
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
