<?php
namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractRemoved;

class Removed extends AbstractRemoved
{
    protected static $version = '5.6.0';

    /**
     * {Description}
     * A number of constants marked obsolete in the cURL library have now been
     * removed
     *
     * {Reference}
     * http://php.net/manual/en/migration56.extensions.php#migration56.extensions.curl
     */
    protected $constTable = array(
        'CURLOPT_CLOSEPOLICY', 'CURLCLOSEPOLICY_CALLBACK',
        'CURLCLOSEPOLICY_LEAST_RECENTLY_USED', 'CURLCLOSEPOLICY_LEAST_TRAFFIC',
        'CURLCLOSEPOLICY_OLDEST', 'CURLCLOSEPOLICY_SLOWEST',
    );
}
