<?php
namespace PhpMigration\Changes\v5dot5;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractRemoved;

class Removed extends AbstractRemoved
{
    protected static $version = '5.5.0';

    /**
     * {Description}
     * The GUIDs that previously resulted in PHP outputting various logos have
     * been removed. This includes the removal of the functions to return those
     * GUIDs.
     *
     * {Reference}
     * http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.guid
     */
    protected $funcTable = array(
        'php_logo_guid',
        'php_egg_logo_guid',
        'php_real_logo_guid',
        'zend_logo_guid',
    );
}
