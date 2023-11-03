<?php declare(strict_types=1);
/**
 * Copyright (c) 2015 · Kerem Güneş
 * Apache License 2.0 · http://github.com/froq/froq-encoding
 */
namespace froq\encoding;

/**
 * An enum class, provides encoding related constants.
 *
 * @package froq\encoding
 * @class   froq\encoding\Encoding
 * @author  Kerem Güneş
 * @since   6.0
 * @enum
 */
class Encoding extends \froq\common\object\Enum
{
    /** ASCII. */
    public final const ASCII = 'ascii';

    /** UTF set. */
    public final const UTF_8  = 'utf-8',
                       UTF_16 = 'utf-16',
                       UTF_32 = 'utf-32';

    /** ISO set. */
    public final const ISO_8859_1  = 'iso-8859-1',
                       ISO_8859_2  = 'iso-8859-2',
                       ISO_8859_3  = 'iso-8859-3',
                       ISO_8859_4  = 'iso-8859-4',
                       ISO_8859_5  = 'iso-8859-5',
                       ISO_8859_6  = 'iso-8859-6',
                       ISO_8859_7  = 'iso-8859-7',
                       ISO_8859_8  = 'iso-8859-8',
                       ISO_8859_9  = 'iso-8859-9',
                       ISO_8859_10 = 'iso-8859-10',
                       ISO_8859_11 = 'iso-8859-11',
                       ISO_8859_12 = 'iso-8859-12',
                       ISO_8859_13 = 'iso-8859-13',
                       ISO_8859_14 = 'iso-8859-14',
                       ISO_8859_15 = 'iso-8859-15',
                       ISO_8859_16 = 'iso-8859-16';
}
