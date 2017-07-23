<?php

namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Changes\RemoveTableItemTrait;
use PhpMigration\SymbolTable;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Deprecated extends AbstractChange
{
    use RemoveTableItemTrait;

    protected static $version = '5.5.0';

    protected $funcTable = [
        // intl
        'datefmt_set_timezone_id',

        // mcrypt
        'mcrypt_cbc', 'mcrypt_cfb', 'mcrypt_ecb', 'mcrypt_ofb',
    ];

    protected $mysqlTable = [
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
    ];

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
        $this->mysqlTable = new SymbolTable($this->mysqlTable, SymbolTable::IC);
    }

    public function prepare()
    {
        $this->visitor->callChange('v5dot3\Deprecated', 'removeTableItems', ['funcTable', $this->mysqlTable]);
    }

    public function leaveNode($node)
    {
        if ($node instanceof Expr\FuncCall && $this->mysqlTable->has($node->name)) {
            /*
             * {Description}
             * The original MySQL extension is now deprecated, and will generate
             * E_DEPRECATED errors when connecting to a database. Instead, use the
             * MySQLi or PDO_MySQL extensions.
             *
             * {Reference}
             * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.mysql
             */
            $this->addSpot(
                'DEPRECATED',
                true,
                'The original MySQL extension is deprecated, use MySQLi or PDO_MySQL extensions instead'
            );
        } elseif ($node instanceof Expr\FuncCall && ParserHelper::isSameFunc($node->name, 'preg_replace')) {
            /**
             * {Description}
             * The preg_replace() /e modifier is now deprecated. Instead, use the
             * preg_replace_callback() function.
             *
             * {Reference}
             * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.preg-replace-e
             */
            $affected = true;
            $certain = false;

            if (!isset($node->args[0])) {
                return;
            }
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
            if ($pattern instanceof Scalar\String_ || $pattern instanceof Scalar\EncapsedStringPart) {
                $pattern = $pattern->value;
            }
            // Extract array to strings
            if ($pattern instanceof Expr\Array_) {
                foreach ($pattern->items as $key => $value) {
                    $this->verifyPregReplace($value->value->value);
                }
            } else {
                $this->verifyPregReplace($pattern);
            }
        } elseif ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name)) {
            /*
             * TODO: how to check IntlDateFormatter::setTimeZoneId
             *
             * {Reference}
             * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.intl
             * http://php.net/manual/en/migration55.deprecated.php#migration55.deprecated.mcrypt
             */
            $this->addSpot('DEPRECATED', true, 'Function '.$node->name.'() is deprecated');
        }
    }

    private function verifyPregReplace($pattern)
    {
        $affected = true;
        $certain = false;

        if (is_string($pattern)) {
            $modifier = strrchr($pattern, substr($pattern, 0, 1));
            if ((strpos($modifier, 'e') !== false)) {
                $affected = true;
                $certain = true;
            } else {
                $affected = false;
            }
        }
        if ($affected) {
            $this->addSpot(
                'DEPRECATED',
                $certain,
                'preg_replace() /e modifier is deprecated, use preg_replace_callback() instead.'
            );
        }
    }
}
