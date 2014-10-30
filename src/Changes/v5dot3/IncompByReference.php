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
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class IncompByReference extends Change
{
    protected static $prepared = false;

    protected static $callList;

    protected static $declareTable;

    protected static $methodTable;

    protected static $buildinTable = array(
        // This list is exported by running `phpmig --export-posbit <docfile>`
        'apc_dec'                                =>    4, // 001
        'apc_fetch'                              =>    2, // 01
        'apc_inc'                                =>    4, // 001
        'array_multisort'                        =>    1, // 1
        'array_pop'                              =>    1, // 1
        'array_push'                             =>    1, // 1
        'array_shift'                            =>    1, // 1
        'array_splice'                           =>    1, // 1
        'array_unshift'                          =>    1, // 1
        'array_walk'                             =>    1, // 1
        'array_walk_recursive'                   =>    1, // 1
        'arsort'                                 =>    1, // 1
        'asort'                                  =>    1, // 1
        'call_user_method'                       =>    2, // 01
        'call_user_method_array'                 =>    2, // 01
        'collator_asort'                         =>    2, // 01
        'collator_sort'                          =>    2, // 01
        'collator_sort_with_sort_keys'           =>    2, // 01
        'curl_multi_exec'                        =>    2, // 01
        'curl_multi_info_read'                   =>    2, // 01
        'current'                                =>    1, // 1
        'datefmt_localtime'                      =>    4, // 001
        'datefmt_parse'                          =>    4, // 001
        'dbplus_curr'                            =>    2, // 01
        'dbplus_first'                           =>    2, // 01
        'dbplus_info'                            =>    4, // 001
        'dbplus_last'                            =>    2, // 01
        'dbplus_next'                            =>    2, // 01
        'dbplus_prev'                            =>    2, // 01
        'dbplus_tremove'                         =>    4, // 001
        'dns_get_record'                         =>   28, // 00111
        'each'                                   =>    1, // 1
        'enchant_dict_quick_check'               =>    4, // 001
        'end'                                    =>    1, // 1
        'ereg'                                   =>    4, // 001
        'eregi'                                  =>    4, // 001
        'exec'                                   =>    6, // 011
        'exif_thumbnail'                         =>   14, // 0111
        'expect_expectl'                         =>    4, // 001
        'extract'                                =>    1, // 1
        'flock'                                  =>    4, // 001
        'fscanf'                                 =>    4, // 001
        'fsockopen'                              =>   12, // 0011
        'ftp_alloc'                              =>    4, // 001
        'getimagesize'                           =>    2, // 01
        'getimagesizefromstring'                 =>    2, // 01
        'getmxrr'                                =>    6, // 011
        'gmp_setbit'                             =>    1, // 1
        'gnupg_decryptverify'                    =>    4, // 001
        'gnupg_verify'                           =>    8, // 0001
        'grapheme_extract'                       =>   16, // 00001
        'headers_sent'                           =>    3, // 11
        'http_build_url'                         =>    8, // 0001
        'http_get'                               =>    4, // 001
        'http_head'                              =>    4, // 001
        'http_negotiate_charset'                 =>    2, // 01
        'http_negotiate_content_type'            =>    2, // 01
        'http_negotiate_language'                =>    2, // 01
        'http_post_data'                         =>    8, // 0001
        'http_post_fields'                       =>   16, // 00001
        'http_put_data'                          =>    8, // 0001
        'http_put_file'                          =>    8, // 0001
        'http_put_stream'                        =>    8, // 0001
        'http_request'                           =>   16, // 00001
        'idn_to_ascii'                           =>    8, // 0001
        'idn_to_utf8'                            =>    8, // 0001
        'is_callable'                            =>    4, // 001
        'key'                                    =>    1, // 1
        'krsort'                                 =>    1, // 1
        'ksort'                                  =>    1, // 1
        'ldap_control_paged_result_response'     =>   12, // 0011
        'ldap_get_option'                        =>    4, // 001
        'ldap_parse_reference'                   =>    4, // 001
        'ldap_parse_result'                      =>   60, // 001111
        'm_completeauthorizations'               =>    2, // 01
        'maxdb_stmt_bind_param'                  =>    4, // 001
        'maxdb_stmt_bind_param'                  =>   12, // 0011
        'maxdb_stmt_bind_result'                 =>    6, // 011
        'mb_convert_variables'                   =>   12, // 0011
        'mb_parse_str'                           =>    2, // 01
        'mqseries_back'                          =>    6, // 011
        'mqseries_begin'                         =>   12, // 0011
        'mqseries_close'                         =>   24, // 00011
        'mqseries_cmit'                          =>    6, // 011
        'mqseries_conn'                          =>   14, // 0111
        'mqseries_connx'                         =>   30, // 01111
        'mqseries_disc'                          =>    6, // 011
        'mqseries_get'                           =>  508, // 001111111
        'mqseries_inq'                           =>  928, // 0000010111
        'mqseries_open'                          =>   58, // 010111
        'mqseries_put'                           =>  108, // 0011011
        'mqseries_put1'                          =>  110, // 0111011
        'mqseries_set'                           =>  768, // 0000000011
        'msg_receive'                            =>  148, // 00101001
        'msg_send'                               =>   32, // 000001
        'mssql_bind'                             =>    4, // 001
        'mysqli_poll'                            =>    7, // 111
        'mysqli_stmt_bind_param'                 =>   12, // 0011
        'mysqli_stmt_bind_result'                =>    6, // 011
        'mysqlnd_uh_convert_to_mysqlnd'          =>    1, // 1
        'mysqlnd_uh_set_connection_proxy'        =>    3, // 11
        'mysqlnd_uh_set_statement_proxy'         =>    1, // 1
        'natcasesort'                            =>    1, // 1
        'natsort'                                =>    1, // 1
        'ncurses_color_content'                  =>   14, // 0111
        'ncurses_getmaxyx'                       =>    6, // 011
        'ncurses_getmouse'                       =>    1, // 1
        'ncurses_getyx'                          =>    6, // 011
        'ncurses_instr'                          =>    1, // 1
        'ncurses_mouse_trafo'                    =>    3, // 11
        'ncurses_mousemask'                      =>    2, // 01
        'ncurses_pair_content'                   =>    6, // 011
        'ncurses_wmouse_trafo'                   =>    6, // 011
        'newt_button_bar'                        =>    1, // 1
        'newt_form_run'                          =>    2, // 01
        'newt_get_screen_size'                   =>    3, // 11
        'newt_grid_get_size'                     =>    6, // 011
        'newt_reflow_text'                       =>   48, // 000011
        'newt_win_entries'                       =>   64, // 0000001
        'newt_win_menu'                          =>  128, // 00000001
        'next'                                   =>    1, // 1
        'numfmt_parse'                           =>    8, // 0001
        'numfmt_parse_currency'                  =>   12, // 0011
        'oci_bind_array_by_name'                 =>    4, // 001
        'oci_bind_by_name'                       =>    4, // 001
        'oci_define_by_name'                     =>    4, // 001
        'oci_fetch_all'                          =>    2, // 01
        'odbc_fetch_into'                        =>    2, // 01
        'openssl_csr_export'                     =>    2, // 01
        'openssl_csr_new'                        =>    2, // 01
        'openssl_open'                           =>    2, // 01
        'openssl_pkcs12_export'                  =>    2, // 01
        'openssl_pkcs12_read'                    =>    2, // 01
        'openssl_pkey_export'                    =>    2, // 01
        'openssl_private_decrypt'                =>    2, // 01
        'openssl_private_encrypt'                =>    2, // 01
        'openssl_public_decrypt'                 =>    2, // 01
        'openssl_public_encrypt'                 =>    2, // 01
        'openssl_random_pseudo_bytes'            =>    2, // 01
        'openssl_seal'                           =>    6, // 011
        'openssl_sign'                           =>    2, // 01
        'openssl_spki_export'                    =>    1, // 1
        'openssl_spki_export_challenge'          =>    1, // 1
        'openssl_spki_new'                       =>    3, // 11
        'openssl_spki_verify'                    =>    1, // 1
        'openssl_x509_export'                    =>    2, // 01
        'parse_str'                              =>    2, // 01
        'parsekit_compile_file'                  =>    2, // 01
        'parsekit_compile_string'                =>    2, // 01
        'passthru'                               =>    2, // 01
        'pcntl_sigprocmask'                      =>    4, // 001
        'pcntl_sigtimedwait'                     =>    2, // 01
        'pcntl_sigwaitinfo'                      =>    2, // 01
        'pcntl_wait'                             =>    1, // 1
        'pcntl_waitpid'                          =>    2, // 01
        'pfsockopen'                             =>   12, // 0011
        'php_check_syntax'                       =>    2, // 01
        'preg_filter'                            =>   16, // 00001
        'preg_match'                             =>    4, // 001
        'preg_match_all'                         =>    4, // 001
        'preg_replace'                           =>   16, // 00001
        'preg_replace_callback'                  =>   16, // 00001
        'prev'                                   =>    1, // 1
        'proc_open'                              =>    4, // 001
        'reset'                                  =>    1, // 1
        'rsort'                                  =>    1, // 1
        'settype'                                =>    1, // 1
        'shuffle'                                =>    1, // 1
        'similar_text'                           =>    4, // 001
        'socket_create_pair'                     =>    8, // 0001
        'socket_getpeername'                     =>    6, // 011
        'socket_getsockname'                     =>    6, // 011
        'socket_recv'                            =>    2, // 01
        'socket_recvfrom'                        =>   50, // 010011
        'socket_select'                          =>    7, // 111
        'sort'                                   =>    1, // 1
        'sqlite_exec'                            =>    4, // 001
        'sqlite_factory'                         =>    4, // 001
        'sqlite_open'                            =>    4, // 001
        'sqlite_popen'                           =>    4, // 001
        'sqlite_query'                           =>    8, // 0001
        'sqlite_unbuffered_query'                =>    8, // 0001
        'sscanf'                                 =>    4, // 001
        'str_ireplace'                           =>    8, // 0001
        'str_replace'                            =>    8, // 0001
        'stream_select'                          =>    7, // 111
        'stream_socket_accept'                   =>    4, // 001
        'stream_socket_client'                   =>    6, // 011
        'stream_socket_recvfrom'                 =>    8, // 0001
        'stream_socket_server'                   =>    6, // 011
        'system'                                 =>    2, // 01
        'taint'                                  =>    1, // 1
        'uasort'                                 =>    1, // 1
        'uksort'                                 =>    1, // 1
        'untaint'                                =>    1, // 1
        'usort'                                  =>    1, // 1
        'wincache_ucache_dec'                    =>    4, // 001
        'wincache_ucache_get'                    =>    2, // 01
        'wincache_ucache_inc'                    =>    4, // 001
        'xdiff_string_merge3'                    =>    8, // 0001
        'xdiff_string_patch'                     =>    8, // 0001
        'xml_parse_into_struct'                  =>   12, // 0011
        'xml_set_object'                         =>    2, // 01
        'xmlrpc_decode_request'                  =>    2, // 01
        'xmlrpc_set_type'                        =>    1, // 1
        'yaml_parse'                             =>    4, // 001
        'yaml_parse_file'                        =>    4, // 001
        'yaml_parse_url'                         =>    4, // 001
        'yaz_ccl_parse'                          =>    4, // 001
        'yaz_hits'                               =>    2, // 01
        'yaz_scan_result'                        =>    2, // 01
        'yaz_wait'                               =>    1, // 1
    );

    public function prepare()
    {
        if (!static::$prepared) {
            static::$callList = array();
            static::$declareTable = new SymbolTable(static::$buildinTable, SymbolTable::IC);
            static::$methodTable = new SymbolTable(array(), SymbolTable::IC);
        }
    }

    public function leaveNode($node)
    {
        // Populate
        if ($node instanceof Stmt\Function_) {
            $this->populateDefine($node, 'func');
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->populateDefine($node, 'method');
        } elseif ($node instanceof Expr\FuncCall) {
            $this->populateCall($node, 'func');
        } elseif ($node instanceof Expr\StaticCall) {
            $this->populateCall($node, 'static');
        } elseif ($node instanceof Expr\MethodCall) {
            $this->populateCall($node, 'method');
        }
    }

    public function finish()
    {
        // Check all call
        foreach (static::$callList as $call) {
            $cname = $call['name'];
            if (static::$declareTable->has($cname)) {
                if ($this->isMismatch(static::$declareTable->get($cname), $call['pos'])) {
                    $this->emitSpot($call);
                }
            } elseif (substr($cname, 0, 2) == '->' && static::$methodTable->has($cname)) {
                $suspect = array();
                foreach (static::$methodTable->get($cname) as $class => $posbit) {
                    if ($this->isMismatch($posbit, $call['pos'])) {
                        $suspect[] = $class;
                    }
                }
                if ($suspect) {
                    $this->emitSpot($call, $suspect);
                }
            }
        }
    }

    protected function emitSpot($call, $suspect = null)
    {
        /*
         * {Description}
         * The behaviour of functions with by-reference parameters called by
         * value has changed. Where previously the function would accept the
         * by-value argument, a fatal error is now emitted. Any previous code
         * passing constants or literals to functions expecting references,
         * will need altering to assign the value to a variable before calling
         * the function.
         *
         * {Errmsg}
         * Fatal error: Only variables can be passed by reference
         *
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        if ($suspect) {
            $message = 'Only variables can be passed by reference, when %s called by instance %s';
            $message = sprintf($message, $call['name'], implode(', ', $suspect));
        } else {
            $message = 'Only variables can be passed by reference';
        }

        $this->visitor->addSpot($message, $call['line'], $call['file']);
    }

    protected function emitPassByRef($node)
    {
        /*
         * {Description}
         * Call-time pass-by-reference is now deprecated
         *
         * {Reference}
         * http://php.net/manual/en/migration53.deprecated.php
         */
        $this->visitor->addSpot('Calltime pass-by-reference is deprecated');
    }

    protected function checkPassByRef($node)
    {
        foreach ($node->args as $arg) {
            if ($arg->byRef) {
                return $this->emitPassByRef($node);
            }
        }
    }

    protected function positionWithRef($node)
    {
        $posbit = 0;
        foreach ($node->params as $pos => $param) {
            if ($param->byRef == 1) {
                $posbit |= 1 << $pos;
            }
        }
        return $posbit;
    }

    protected function positionByValue($node)
    {
        $posbit = 0;
        foreach ($node->args as $pos => $arg) {
            if ($arg->value instanceof Expr\Variable ||
                $arg->value instanceof Expr\PropertyFetch ||
                $arg->value instanceof Expr\StaticPropertyFetch ||
                $arg->value instanceof Expr\ArrayDimFetch ||
                $arg->value instanceof Expr\FuncCall ||
                $arg->value instanceof Expr\MethodCall ||
                $arg->value instanceof Expr\StaticCall) {
                continue;
            } elseif ($arg->value instanceof Expr\Assign) {
                // Variable in assign expression
                if ($arg->value->var instanceof Expr\Variable) {
                    continue;
                }
            }
            $posbit |= 1 << $pos;
        }
        return $posbit;
    }

    protected function isMismatch($define, $call)
    {
        return true && ($define & $call);
    }

    protected function populateDefine($node, $type)
    {
        $posbit = $this->positionWithRef($node);
        if (!$posbit) {
            return;
        }

        if ($type == 'func') {
            $fname = $node->name;
        } elseif ($node->isStatic()) {
            $fname = $this->visitor->getClassname().'::'.$node->name;
        } else {
            $fname = $this->visitor->getClassname().'->'.$node->name;

            $mname = '->'.$node->name;
            if (static::$methodTable->has($mname)) {
                $suspect = static::$methodTable->get($mname);
            } else {
                $suspect = array();
            }
            $suspect[$this->visitor->getClassname()] = $posbit;
            static::$methodTable->set($fname, $suspect);
        }

        static::$declareTable->set($fname, $posbit);
    }

    protected function populateCall($node, $type)
    {
        $this->checkPassByRef($node);

        if (ParserHelper::isDynamicCall($node)) {
            return;
        }

        $posbit = $this->positionByValue($node);
        if (!$posbit) {
            return;
        }

        if ($type == 'func') {
            $callname = $node->name;
        } elseif ($type == 'static') {
            $class = $node->class->toString();
            if ($class == 'self' && $this->visitor->inClass()) {
                $class = $this->visitor->getClassname();
            }
            $callname = $class.'::'.$node->name;
        } elseif ($type == 'method') {
            $object = $node->var->name;
            if ($object == 'this' && $this->visitor->inClass()) {
                $object = $this->visitor->getClassname();
            } else {
                $object = '';
            }
            $callname = $object.'->'.$node->name;
        }

        static::$callList[] = array(
            'name' => $callname,
            'pos' => $posbit,
            'file' => $this->visitor->getFile(),
            'line' => $node->getLine(),
        );
    }
}
