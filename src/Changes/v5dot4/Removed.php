<?php
namespace PhpMigration\Changes\v5dot4;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractRemoved;

class Removed extends AbstractRemoved
{
    protected static $version = '5.4.0';

    /**
     * {Reference}
     * http://php.net/manual/en/migration54.incompatible.php
     */
    protected $funcTable = array(
        'define_syslog_variables',
        'import_request_variables',
        'session_is_registered',
        'session_register',
        'session_unregister',
        'mysqli_bind_param',
        'mysqli_bind_result',
        'mysqli_client_encoding',
        'mysqli_fetch',
        'mysqli_param_count',
        'mysqli_get_metadata',
        'mysqli_send_long_data',
    );
}
