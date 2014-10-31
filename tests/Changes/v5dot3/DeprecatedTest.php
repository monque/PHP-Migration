<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Lexer;
use PhpParser\Parser;

class DeprecatedTest extends \PHPUnit_Framework_TestCase
{
    protected $parser;

    protected $change;

    protected $callList;

    protected function setUp()
    {
        $this->parser = new Parser(new Lexer\Emulative);

        $this->change = new Deprecated();
        $this->change->prepare();

        $this->callList = array(
            'call_user_method'          => 'use call_user_func() instead',
            'call_user_method_array'    => 'use call_user_func_array() instead',
            'define_syslog_variables'   => '',
            'dl'                        => 'available only under CLI, CGI, and embed SAPIs',
            'ereg'                      => 'use preg_match() instead',
            'ereg_replace'              => 'use preg_replace() instead',
            'eregi'                     => 'use preg_match() with the "i" modifier instead',
            'eregi_replace'             => 'use preg_replace() with the "i" modifier instead',
            'set_magic_quotes_runtime'  => '',
            'magic_quotes_runtime'      => '',
            'session_register'          => 'use the $_SESSION superglobal instead',
            'session_unregister'        => 'use the $_SESSION superglobal instead',
            'session_is_registered'     => 'use the $_SESSION superglobal instead',
            'set_socket_blocking'       => 'use stream_set_blocking() instead',
            'split'                     => 'use preg_split() instead',
            'spliti'                    => 'use preg_split() with the "i" modifier instead',
            'sql_regcase'               => '',
            'mysql_db_query'            => 'use mysql_select_db() and mysql_query() instead',
            'mysql_escape_string'       => 'use mysql_real_escape_string() instead',
        );
    }

    protected function genFuncCall($name)
    {
        $code = sprintf('<?php %s();', $name);
        $stmts = $this->parser->parse($code);
        return $stmts[0];
    }

    public function testDeprecatedFunc()
    {
        foreach ($this->callList as $name => $advice) {
            // Normal name
            $node = $this->genFuncCall($name);
            $this->assertTrue($this->change->isDeprecatedFunc($node));

            // Messy name
            $messyname = substr($name, 0, -1).strtoupper(substr($name, -1));
            $node = $this->genFuncCall($messyname);
            $this->assertTrue($this->change->isDeprecatedFunc($node));
        }

        // Indeprecated name
        $node = $this->genFuncCall('ohshit');
        $this->assertFalse($this->change->isDeprecatedFunc($node));
    }

    public function testAssignNewByRef()
    {
        // Direct assign
        $node = current($this->parser->parse('<?php $o = new Class_();'));
        $this->assertFalse($this->change->isAssignNewByRef($node));

        // By-reference
        $node = current($this->parser->parse('<?php $o = &new Class_();'));
        $this->assertTrue($this->change->isAssignNewByRef($node));
    }
}
