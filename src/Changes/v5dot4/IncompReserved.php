<?php
namespace PhpMigration\Changes\v5dot4;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractKeywordReserved;

class IncompReserved extends AbstractKeywordReserved
{
    protected static $version = '5.4.0';

    /**
     * {Description}
     * The following keywords are now reserved, and may not be used as names by
     * functions, classes, etc.
     * trait, callable, insteadof
     *
     * {Reference}
     * http://php.net/manual/en/migration54.incompatible.php
     */
    protected $wordTable = array(
        'trait', 'callable', 'insteadof'
    );
}
