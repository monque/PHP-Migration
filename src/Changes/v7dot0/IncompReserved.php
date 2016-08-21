<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractKeywordReserved;

class IncompReserved extends AbstractKeywordReserved
{
    protected static $version = '7.0.0';

    /* The following names cannot be used to name classes, interfaces or
     * traits:
     *
     * bool, int, float, string, NULL, TRUE, FALSE
     *
     * Furthermore, the following names should not be used. Although they will
     * not generate an error in PHP 7.0, they are reserved for future use and
     * should be considered deprecated.
     *
     * resource, object, mixed, numeric
     *
     * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.classes
     */
    protected $wordTable = array(
        'bool', 'int', 'float', 'string', 'NULL', 'TRUE', 'FALSE',
    );
}
