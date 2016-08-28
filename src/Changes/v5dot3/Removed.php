<?php

namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractRemoved;

class Removed extends AbstractRemoved
{
    protected static $version = '5.3.0';

    /**
     * {Description}
     * The dl() function is now disabled by default, and is now available
     * only under the CLI, CGI, and embed SAPIs.
     *
     * {Reference}
     * http://php.net/manual/en/migration53.sapi.php
     */
    protected $funcTable = [
        'dl'
    ];
}
