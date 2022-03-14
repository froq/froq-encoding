<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
declare(strict_types=1);

namespace froq\encoding;

/**
 * Encoding.
 *
 * @package froq\encoding
 * @object  froq\encoding\Encoding
 * @author  Kerem Güneş
 * @since   6.0
 */
class Encoding extends \StaticClass
{
    /**
     * Types.
     * @const string
     */
    final const TYPE_UTF_8 = 'UTF-8',
                TYPE_ASCII = 'ASCII',
}
