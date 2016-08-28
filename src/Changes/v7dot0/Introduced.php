<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractIntroduced;

class Introduced extends AbstractIntroduced
{
    protected static $version = '7.0.0';

    /** @see http://php.net/manual/en/migration70.new-functions.php */
    protected $funcTable = [
        // CSPRNG
        'random_bytes', 'random_int',

        // Error Handling and Logging
        'error_clear_last',

        // GNU Multiple Precision
        'gmp_random_seed',

        // Math
        'intdiv',

        // PCRE
        'preg_replace_callback_array',

        // PHP Options/Info
        'gc_mem_caches', 'get_resources',

        // POSIX
        'posix_setrlimit',

        // Zlib Compression
        'inflate_add', 'deflate_add', 'inflate_init', 'deflate_init',
    ];

    protected $methodTable = [
        // Generator
        'Generator::getReturn',

        // Closure
        'Closure::call',

        // Reflection
        'ReflectionParameter::getType', 'ReflectionParameter::hasType',
        'ReflectionFunctionAbstract::getReturnType',
        'ReflectionFunctionAbstract::hasReturnType',

        // Zip
        'ZipArchive::setCompressionIndex', 'ZipArchive::setCompressionName',
    ];

    /** @see http://php.net/manual/en/migration70.classes.php */
    protected $classTable = [
        // Intl
        'IntlChar',

        // Reflection
        'ReflectionGenerator', 'ReflectionType',

        // Session Handling
        'SessionUpdateTimestampHandlerInterface',

        // Exception Hierarchy
        'Throwable', 'Error', 'TypeError', 'ParseError', 'AssertionError',
        'ArithmeticError', 'DivisionByZeroError',
    ];

    /** @see http://php.net/manual/en/migration70.constants.php */
    protected $constTable = [
        // Core Predefined Constants
        'PHP_INT_MIN',

        // GD
        'IMG_WEBP', // (as of PHP 7.0.10)

        // LibXML
        'LIBXML_BIGLINES',

        // PCRE
        'PREG_JIT_STACKLIMIT_ERROR',

        // POSIX
        'POSIX_RLIMIT_AS', 'POSIX_RLIMIT_CORE', 'POSIX_RLIMIT_CPU',
        'POSIX_RLIMIT_DATA', 'POSIX_RLIMIT_FSIZE', 'POSIX_RLIMIT_LOCKS',
        'POSIX_RLIMIT_MEMLOCK', 'POSIX_RLIMIT_MSGQUEUE', 'POSIX_RLIMIT_NICE',
        'POSIX_RLIMIT_NOFILE', 'POSIX_RLIMIT_NPROC', 'POSIX_RLIMIT_RSS',
        'POSIX_RLIMIT_RTPRIO', 'POSIX_RLIMIT_RTTIME',
        'POSIX_RLIMIT_SIGPENDING', 'POSIX_RLIMIT_STACK',
        'POSIX_RLIMIT_INFINITY',

        // Zlib
        'ZLIB_ENCODING_RAW', 'ZLIB_ENCODING_DEFLATE', 'ZLIB_ENCODING_GZIP',
        'ZLIB_FILTERED', 'ZLIB_HUFFMAN_ONLY', 'ZLIB_FIXED', 'ZLIB_RLE',
        'ZLIB_DEFAULT_STRATEGY', 'ZLIB_BLOCK', 'ZLIB_FINISH',
        'ZLIB_FULL_FLUSH', 'ZLIB_NO_FLUSH', 'ZLIB_PARTIAL_FLUSH',
        'ZLIB_SYNC_FLUSH',
    ];
}
