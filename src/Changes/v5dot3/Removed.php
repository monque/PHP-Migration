<?php
namespace PhpMigration\Changes\v5dot3;

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
    protected static $version = '5.3.0';

    /**
     * {Description}
     * The dl() function is now disabled by default, and is now available
     * only under the CLI, CGI, and embed SAPIs.
     *
     * {Reference}
     * http://php.net/manual/en/migration53.sapi.php
     */
    protected $funcTable = array(
        'dl'
    );
}
