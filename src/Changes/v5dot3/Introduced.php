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
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class Introduced extends Change
{
    protected static $function = array(
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

    protected static $class = array(
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

    protected static $const = array(
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

    public function leaveNode($node)
    {
        if ($node instanceof Stmt\Function_ && in_array($node->name, static::$function)) {
            // Function
            $this->visitor->addSpot(sprintf('Cannot redeclare %s()', $node->name));
        } elseif ($node instanceof Stmt\Class_ && in_array($node->name, static::$class)) {
            // Class
            $this->visitor->addSpot(sprintf('Cannot redeclare class %s', $node->name));
        } elseif ($node instanceof Expr\FuncCall && $node->name == 'define') {
            // Constant
            $constname = $node->args[0]->value->value;
            if (in_array($constname, static::$const)) {
                $this->visitor->addSpot(sprintf('Constant %s already defined', $constname));
            }
        }
    }
}
