<?php

namespace PhpMigration\Changes\v7dot1;

use PhpMigration\Changes\AbstractIntroduced;

class Introduced extends AbstractIntroduced
{
    protected static $version = '7.1.0';

    /** @see http://php.net/manual/en/migration71.new-functions.php */
    protected $funcTable = [
        // CURL
        'curl_multi_errno', 'curl_share_errno', 'curl_share_strerror',

        // SPL
        'is_iterable',

        // PCNTL
        'pcntl_async_signals',
    ];

    protected $methodTable = [
        // Closure
        'Closure::fromCallable',
    ];

    /** @see http://php.net/manual/en/migration71.constants.php */
    protected $constTable = [
        // CURL
        'CURLMOPT_PUSHFUNCTION', 'CURL_PUST_OK', 'CURL_PUSH_DENY',

        // SPL
        'MT_RAND_PHP',
    ];
}
