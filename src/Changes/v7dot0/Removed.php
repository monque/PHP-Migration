<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractRemoved;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Removed extends AbstractRemoved
{
    protected static $version = '7.0.0';

    protected $funcTable = array(
        /* call_user_method() and call_user_method_array()
         *
         * These functions were deprecated in PHP 4.1.0 in favour of
         * call_user_func() and call_user_func_array(). You may also want to
         * consider using variable functions and/or the ... operator. */
        'call_user_method', 'call_user_method_array',

        /* All ereg* functions
         *
         * All ereg functions were removed. PCRE is a recommended
         * alternative.*/
        'ereg', 'ereg_replace', 'eregi', 'eregi_replace',

        /* mcrypt aliases
         *
         * The deprecated mcrypt_generic_end() function has been removed in
         * favour of mcrypt_generic_deinit().
         *
         * Additionally, the deprecated mcrypt_ecb(), mcrypt_cbc(),
         * mcrypt_cfb() and mcrypt_ofb() functions have been removed in favour
         * of using mcrypt_decrypt() with the appropriate MCRYPT_MODE_*
         * constant. */
        'mcrypt_generic_end', 'mcrypt_ecb', 'mcrypt_cbc', 'mcrypt_cfb',
        'mcrypt_ofb',

        /* All ext/mysql functions
         *
         * All ext/mysql functions were removed. For details about choosing a
         * different MySQL API, see Choosing a MySQL API. */
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

        /* All ext/mssql functions
         *
         * All ext/mssql functions were removed. For a list of alternatives,
         * see the MSSQL Introduction. */
        'mssql_bind', 'mssql_close', 'mssql_connect', 'mssql_data_seek',
        'mssql_execute', 'mssql_fetch_array', 'mssql_fetch_assoc',
        'mssql_fetch_batch', 'mssql_fetch_field', 'mssql_fetch_object',
        'mssql_fetch_row', 'mssql_field_length', 'mssql_field_name',
        'mssql_field_seek', 'mssql_field_type', 'mssql_free_result',
        'mssql_free_statement', 'mssql_get_last_message', 'mssql_guid_string',
        'mssql_init', 'mssql_min_error_severity', 'mssql_min_message_severity',
        'mssql_next_result', 'mssql_num_fields', 'mssql_num_rows',
        'mssql_pconnect', 'mssql_query', 'mssql_result', 'mssql_rows_affected',
        'mssql_select_db',

        /* intl aliases
         *
         * The deprecated datefmt_set_timezone_id() and
         * IntlDateFormatter::setTimeZoneID() aliases have been removed in
         * favour of datefmt_set_timezone() and
         * IntlDateFormatter::setTimeZone(), respectively. */
        'datefmt_set_timezone_id',
        // TODO 'IntlDateFormatter::setTimeZoneID',

        /* set_magic_quotes_runtime()
         *
         * set_magic_quotes_runtime(), along with its alias
         * magic_quotes_runtime(), have been removed. They were deprecated in
         * PHP 5.3.0, and became effectively non-functional with the removal of
         * magic quotes in PHP 5.4.0. */
        'set_magic_quotes_runtime', 'magic_quotes_runtime',

        /* set_socket_blocking()
         *
         * The deprecated set_socket_blocking() alias has been removed in
         * favour of stream_set_blocking(). */
        'set_socket_blocking',

        /* dl() in PHP-FPM
         *
         * dl() can no longer be used in PHP-FPM. It remains functional in the
         * CLI and embed SAPIs.
         *
         * It has already disabled in 5.3, ignore it here */

        /* GD Type1 functions
         *
         * Support for PostScript Type1 fonts has been removed from the GD
         * extension, resulting in the removal of the following functions */
        'imagepsbbox', 'imagepsencodefont', 'imagepsextendfont',
        'imagepsfreefont', 'imagepsloadfont', 'imagepsslantfont',
        'imagepstext',
    );

    public function prepare()
    {
        parent::prepare();

        if ($this->visitor) {
            $this->visitor->callChange('v5dot3\Deprecated', 'skipDeprecatedFuncs', $this->funcTable);
            $this->visitor->callChange('v5dot4\Deprecated', 'skipDeprecatedFuncs', $this->funcTable);
            $this->visitor->callChange('v5dot5\Deprecated', 'skipDeprecatedFuncs', $this->funcTable);
            $this->visitor->callChange('v5dot5\Deprecated', 'skipMysqlFuncs', $this->funcTable);
            $this->visitor->callChange('v5dot6\IncompMisc', 'skipMcryptFuncs', $this->funcTable);
            $this->visitor->callChange('v5dot6\Deprecated', 'skipHRPD', true);
        }
    }

    public function leaveNode($node)
    {
        parent::leaveNode($node);

        // TODO move to AbstractRemoved
        if ($node instanceof Expr\Variable && !($node->name instanceof Expr\Variable) &&
                $node->name == 'HTTP_RAW_POST_DATA') {
            $this->addSpot('WARNING', true, '$HTTP_RAW_POST_DATA is removed, use php://input instead');
        }
    }
}
