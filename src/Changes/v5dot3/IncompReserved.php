<?php

namespace PhpMigration\Changes\v5dot3;

use PhpMigration\Changes\AbstractKeywordReserved;

class IncompReserved extends AbstractKeywordReserved
{
    protected static $version = '5.3.0';

    /**
     * {Description}
     * The following keywords are now reserved and may not be used in function, class, etc. names.
     * goto, namespace
     *
     * {Reference}
     * http://php.net/manual/en/migration53.incompatible.php
     */
    protected $wordTable = [
        'goto', 'namespace',
    ];
}
