<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractRemoved;

class Removed extends AbstractRemoved
{
    protected static $version = '5.4.0';

    /**
     * {Reference}
     * http://php.net/manual/en/migration54.incompatible.php.
     */
    protected $funcTable = [
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
    ];
}
