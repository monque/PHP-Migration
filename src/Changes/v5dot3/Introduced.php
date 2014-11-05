<?php
namespace PhpMigration\Changes\v5dot3;

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
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class Introduced extends Change
{
    protected static $version = '5.3.0';

    protected static $prepared = false;

    public static $funcTable = array(
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

    public static $classTable = array(
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

    public static $constTable = array(
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

    public static $paramTable = array(
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

    protected $condFunc = null;

    protected $condClass = null;

    public function prepare()
    {
        if (!static::$prepared) {
            static::$funcTable  = new SymbolTable(array_flip(static::$funcTable), SymbolTable::IC);
            static::$classTable = new SymbolTable(array_flip(static::$classTable), SymbolTable::IC);
            static::$constTable = new SymbolTable(array_flip(static::$constTable), SymbolTable::CS);
            static::$paramTable = new SymbolTable(static::$paramTable, SymbolTable::IC);
            static::$prepared = true;
        }
    }

    public function enterNode($node)
    {
        // Support the simplest conditional declaration
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = ParserHelper::getConditionalName($node);
        } elseif (ParserHelper::isConditionalClass($node)) {
            $this->condClass = ParserHelper::getConditionalName($node);
        }
    }

    public function leaveNode($node)
    {
        /*
         * Function
         *
         * {Reference}
         * http://php.net/manual/en/migration53.functions.php
         */
        if ($this->isNewFunc($node)) {
            $this->addSpot('FATAL', sprintf('Cannot redeclare %s()', $node->name));

        /*
         * Class
         *
         * {Reference}
         * http://php.net/manual/en/migration53.classes.php
         */
        } elseif ($this->isNewClass($node)) {
            $this->addSpot('FATAL', sprintf('Cannot redeclare class %s', $node->name));

        /*
         * Constant
         *
         * {Reference}
         * http://php.net/manual/en/migration53.global-constants.php
         */
        } elseif ($this->isNewConst($node)) {
            $constname = $node->args[0]->value->value;
            $this->addSpot('WARNING', sprintf('Constant %s already defined', $constname));

        /*
         * Parameter
         *
         * {Reference}
         * http://php.net/manual/en/migration53.parameters.php
         */
        } elseif ($this->isNewParam($node)) {
            $advice = static::$paramTable->get($node->name);
            $this->addSpot('NEW', sprintf('Function %s() has new parameter, %s', $node->name, $advice));
        }

        // Conditional declaration clear
        if (ParserHelper::isConditionalFunc($node)) {
            $this->condFunc = null;
        } elseif (ParserHelper::isConditionalClass($node)) {
            $this->condClass = null;
        }
    }

    public function isNewFunc($node)
    {
        return ($node instanceof Stmt\Function_ && static::$funcTable->has($node->name) &&
            (is_null($this->condFunc) || !NameHelper::isSameFunc($node->name, $this->condFunc)));
    }

    public function isNewClass($node)
    {
        return ($node instanceof Stmt\Class_ && static::$classTable->has($node->name) &&
            (is_null($this->condClass) || !NameHelper::isSameClass($node->name, $this->condClass)));
    }

    public function isNewConst($node)
    {
        if ($node instanceof Expr\FuncCall && NameHelper::isSameFunc($node->name, 'define')) {
            $constname = $node->args[0]->value->value;
            return static::$constTable->has($constname);
        }
        return false;
    }

    public function isNewParam($node)
    {
        return ($node instanceof Expr\FuncCall && static::$paramTable->has($node->name));
    }
}
