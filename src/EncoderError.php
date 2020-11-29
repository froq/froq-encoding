<?php
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 <https://opensource.org/licenses/apache-2.0>
 */
declare(strict_types=1);

namespace froq\encoding;

use froq\common\Error;

/**
 * Encoder Error.
 *
 * @package froq\encoding
 * @object  froq\encoding\EncoderError
 * @author  Kerem Güneş <k-gun@mail.com>
 * @since   4.0
 */
final class EncoderError extends Error
{
    /**
     * Types.
     * @const int
     */
    public const JSON = 1,
                 XML  = 2,
                 GZIP = 3;
}
