<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractIntroduced;
use PhpMigration\SymbolTable;

class Introduced extends AbstractIntroduced
{
    protected static $version = '5.3.0';

    protected $funcTable = array(
        // PHP Core
        'array_replace', 'array_replace_recursive', 'class_alias',
        'forward_static_call', 'forward_static_call_array',
        'gc_collect_cycles', 'gc_disable', 'gc_enable', 'gc_enabled',
        'get_called_class', 'gethostname', 'header_remove', 'lcfirst',
        'parse_ini_string', 'quoted_printable_encode', 'str_getcsv',
        'stream_context_set_default', 'stream_supports_lock',
        'stream_context_get_params',

        // Date/Time
        'date_add', 'date_create_from_format', 'date_diff',
        'date_get_last_errors', 'date_parse_from_format', 'date_sub',
        'timezone_version_get',

        // GMP
        'gmp_testbit',

        // Hash
        'hash_copy',

        // Imap
        'imap_gc', 'imap_utf8_to_mutf7', 'imap_mutf7_to_utf8',

        // Json
        'json_last_error',

        // MySQL Improved
        'mysqli_fetch_all', 'mysqli_get_connection_stats',
        'mysqli_poll', 'mysqli_reap_async_query',

        // OpenSSL
        'openssl_random_pseudo_bytes',

        // PCNTL
        'pcntl_signal_dispatch', 'pcntl_sigprocmask', 'pcntl_sigtimedwait',
        'pcntl_sigwaitinfo',

        // PCRE
        'preg_filter',

        // Semaphore
        'msg_queue_exists', 'shm_has_var',
    );

    protected $methodTable = array(
        // Date/Time
        'DateTime::add', 'DateTime::createFromFormat', 'DateTime::diff',
        'DateTime::getLastErrors', 'DateTime::sub',

        // Exception
        'Exception::getPrevious',

        // DOM
        'DOMNode::getLineNo',

        // PDO_FIREBIRD
        'PDO::setAttribute',

        // Reflection
        'ReflectionClass::getNamespaceName', 'ReflectionClass::getShortName',
        'ReflectionClass::inNamespace', 'ReflectionFunction::getNamespaceName',
        'ReflectionFunction::getShortName', 'ReflectionFunction::inNamespace',
        'ReflectionProperty::setAccessible',

        // SPL
        'SplObjectStorage::addAll', 'SplObjectStorage::removeAll',

        // XSL
        'XSLTProcessor::setProfiling',
    );

    protected $classTable = array(
        // Date/Time
        'DateInterval', 'DatePeriod',

        // Phar
        'Phar', 'PharData', 'PharException', 'PharFileInfo',

        // SPL
        'FilesystemIterator', 'GlobIterator', 'MultipleIterator',
        'RecursiveTreeIterator', 'SplDoublyLinkedList', 'SplFixedArray',
        'SplHeap', 'SplMaxHeap', 'SplMinHeap', 'SplPriorityQueue', 'SplQueue',
        'SplStack',
    );

    protected $constTable = array(
        // PHP Core
        '__DIR__', '__NAMESPACE__', 'E_DEPRECATED', 'E_USER_DEPRECATED',
        'INI_SCANNER_NORMAL', 'INI_SCANNER_RAW', 'PHP_MAXPATHLEN',
        'PHP_WINDOWS_NT_DOMAIN_CONTROLLER', 'PHP_WINDOWS_NT_SERVER',
        'PHP_WINDOWS_NT_WORKSTATION', 'PHP_WINDOWS_VERSION_BUILD',
        'PHP_WINDOWS_VERSION_MAJOR', 'PHP_WINDOWS_VERSION_MINOR',
        'PHP_WINDOWS_VERSION_PLATFORM', 'PHP_WINDOWS_VERSION_PRODUCTTYPE',
        'PHP_WINDOWS_VERSION_SP_MAJOR', 'PHP_WINDOWS_VERSION_SP_MINOR',
        'PHP_WINDOWS_VERSION_SUITEMASK',

        // cURL
        'CURLOPT_PROGRESSFUNCTION',

        // GD
        'IMG_FILTER_PIXELATE',

        // JSON
        'JSON_ERROR_CTRL_CHAR', 'JSON_ERROR_DEPTH', 'JSON_ERROR_NONE',
        'JSON_ERROR_STATE_MISMATCH', 'JSON_ERROR_SYNTAX', 'JSON_FORCE_OBJECT',
        'JSON_HEX_TAG', 'JSON_HEX_AMP', 'JSON_HEX_APOS', 'JSON_HEX_QUOT',

        // LDAP
        'LDAP_OPT_NETWORK_TIMEOUT',

        // libxml
        'LIBXML_LOADED_VERSION',

        // PCRE
        'PREG_BAD_UTF8_OFFSET_ERROR',

        // PCNTL
        'BUS_ADRALN', 'BUS_ADRERR', 'BUS_OBJERR', 'CLD_CONTIUNED',
        'CLD_DUMPED', 'CLD_EXITED', 'CLD_KILLED', 'CLD_STOPPED', 'CLD_TRAPPED',
        'FPE_FLTDIV', 'FPE_FLTINV', 'FPE_FLTOVF', 'FPE_FLTRES', 'FPE_FLTSUB',
        'FPE_FLTUND', 'FPE_INTDIV', 'FPE_INTOVF', 'ILL_BADSTK', 'ILL_COPROC',
        'ILL_ILLADR', 'ILL_ILLOPC', 'ILL_ILLOPN', 'ILL_ILLTRP', 'ILL_PRVOPC',
        'ILL_PRVREG', 'POLL_ERR', 'POLL_HUP', 'POLL_IN', 'POLL_MSG',
        'POLL_OUT', 'POLL_PRI', 'SEGV_ACCERR', 'SEGV_MAPERR', 'SI_ASYNCIO',
        'SI_KERNEL', 'SI_MESGQ', 'SI_NOINFO', 'SI_QUEUE', 'SI_SIGIO',
        'SI_TIMER', 'SI_TKILL', 'SI_USER', 'SIG_BLOCK', 'SIG_SETMASK',
        'SIG_UNBLOCK', 'TRAP_BRKPT', 'TRAP_TRACE',
    );

    protected $paramTable = array(
        // PHP Core
        'clearstatcache'            => 'added clear_realpath_cache and filename',
        'copy'                      => 'added a stream context parameter, context',
        'fgetcsv'                   => 'added escape',
        'ini_get_all'               => 'added details',
        'nl2br'                     => 'added is_xhtml',
        'parse_ini_file'            => 'added scanner_mode',
        'round'                     => 'added mode',
        'stream_context_create'     => 'added params',
        'strstr'                    => 'added before_needle',
        'stristr'                   => 'added before_needle',

        // json
        'json_encode'               => 'added options',
        'json_decode'               => 'added depth',

        // Streams
        'stream_select'             => 'now work with user-space stream wrappers',
        'stream_set_blocking'       => 'now work with user-space stream wrappers',
        'stream_set_timeout'        => 'now work with user-space stream wrappers',
        'stream_set_write_buffer'   => 'now work with user-space stream wrappers',

        // sybase_ct
        'sybase_connect'            => 'added new',
    );

    protected function loadTable()
    {
        $this->funcTable  = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
        $this->methodTable  = new SymbolTable(array_flip($this->methodTable), SymbolTable::IC);
        $this->classTable = new SymbolTable(array_flip($this->classTable), SymbolTable::IC);
        $this->constTable = new SymbolTable(array_flip($this->constTable), SymbolTable::CS);

        unset($this->paramTable);  // New parameter is too trivial
        // $this->paramTable = new SymbolTable($this->paramTable, SymbolTable::IC);
    }
}
