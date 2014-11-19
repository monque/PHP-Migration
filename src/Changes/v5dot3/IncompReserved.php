<?php
namespace PhpMigration\Changes\v5dot3;

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
    protected static $version = '5.3.0';

    /**
     * {Description}
     * The following keywords are now reserved and may not be used in function, class, etc. names.
     * goto, namespace
     *
     * {Reference}
     * http://php.net/manual/en/migration53.incompatible.php
     */
    protected $wordTable = array(
        'goto', 'namespace',
    );
}
