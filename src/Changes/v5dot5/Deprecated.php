<?php
namespace PhpMigration\Changes\v5dot5;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\NameHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Deprecated extends Change
{
    protected static $version = '5.5.0';

    protected $tableLoaded = false;

    protected $funcTable = array(
        // intl
        'datefmt_set_timezone_id',

        // mcrypt
        'mcrypt_cbc', 'mcrypt_cfb', 'mcrypt_ecb', 'mcrypt_ofb',
    );

    protected $mysqlTable = array(
        'mysql_affected_rows', 'mysql_client_encoding', 'mysql_close',
        'mysql_connect', 'mysql_create_db', 'mysql_data_seek', 'mysql_db_name',
        'mysql_db_query', 'mysql_drop_db', 'mysql_errno', 'mysql_error',
        'mysql_escape_string', 'mysql_fetch_array', 'mysql_fetch_assoc',
        'mysql_fetch_field', 'mysql_fetch_lengths', 'mysql_fetch_object',
        'mysql_fetch_row', 'mysql_field_flags', 'mysql_field_len',
        'mysql_field_name', 'mysql_field_seek', 'mysql_field_table',
        'mysql_field_type', 'mysql_free_result', 'mysql_get_client_info',
        'mysql_get_host_info', 'mysql_get_proto_info', 'mysql_get_server_info',
        'mysql_info', 'mysql_insert_id', 'mysql_list_dbs', 'mysql_list_fields',
        'mysql_list_processes', 'mysql_list_tables', 'mysql_num_fields',
        'mysql_num_rows', 'mysql_pconnect', 'mysql_ping', 'mysql_query',
        'mysql_real_escape_string', 'mysql_result', 'mysql_select_db',
        'mysql_set_charset', 'mysql_stat', 'mysql_tablename',
        'mysql_thread_id', 'mysql_unbuffered_query',
    );

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->funcTable = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
            $this->mysqlTable = new SymbolTable(array_flip($this->mysqlTable), SymbolTable::IC);
            $this->tableLoaded = true;
        }
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The original MySQL extension is now deprecated, and will generate
         * E_DEPRECATED errors when connecting to a database. Instead, use the
         * MySQLi or PDO_MySQL extensions.
         *
         * {Reference}
         * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.mysql
         */
        if ($node instanceof Expr\FuncCall && $this->mysqlTable->has($node->name)) {
            $this->addSpot('DEPRECATED', 'The original MySQL extension is deprecated, use MySQLi or PDO_MySQL extensions instead');

        /**
         * {Description}
         * The preg_replace() /e modifier is now deprecated. Instead, use the
         * preg_replace_callback() function.
         *
         * {Reference}
         * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.preg-replace-e
         */
        } elseif ($node instanceof Expr\FuncCall && NameHelper::isSameFunc($node->name, 'preg_replace')) {
            $affected = true;

            $pattern = $node->args[0]->value;

            // TODO: shoud be full tested
            // Read right-most if concat, encapsed
            if ($pattern instanceof Expr\BinaryOp\Concat) {
                $pattern = $pattern->right;
            }
            if ($pattern instanceof Scalar\Encapsed) {
                $pattern = end($pattern->parts);
            }
            // Extract to string
            if ($pattern instanceof Scalar\String) {
                $pattern = $pattern->value;
            }
            // Guess whether e in modifier
            if (is_string($pattern)) {
                $modifier = strrchr($pattern, '/');
                $affected = strpos($modifier, 'e') !== false;
            }

            if ($affected) {
                $this->addSpot('DEPRECATED', 'preg_replace() /e modifier is deprecated, use preg_replace_callback() instead');
            }

        /**
         * TODO: how to check IntlDateFormatter::setTimeZoneId
         *
         * {Reference}
         * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.intl
         * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.mcrypt
         */
        } elseif ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name)) {
            $this->addSpot('DEPRECATED', 'Function '.$node->name.'() is deprecated');
        }
    }
}


