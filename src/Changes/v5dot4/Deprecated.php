<?php
namespace PhpMigration\Changes\v5dot4;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Changes\RemoveTableItemTrait;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

class Deprecated extends AbstractChange
{
    use RemoveTableItemTrait;

    protected static $version = '5.4.0';

    protected $funcTable = array(
        'mcrypt_generic_end',
        'mysql_list_dbs',
    );

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        // Function call
        if ($this->isDeprecatedFunc($node)) {
            /**
             * {Errmsg}
             * Deprecated: Function {function} is deprecated
             *
             * {Reference}
             * http://php.net/manual/en/migration54.deprecated.php
             */
            $this->addSpot('WARNING', true, sprintf('Function %s() is deprecated', $node->name));
        }
    }

    protected function isDeprecatedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name));
    }
}
