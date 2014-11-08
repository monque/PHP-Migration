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
use PhpParser\Node\Expr;

class Removed extends Change
{
    protected static $version = '5.5.0';

    protected $tableLoaded = false;

    protected $funcTable = array(
        'php_logo_guid',
        'php_egg_logo_guid',
        'php_real_logo_guid',
        'zend_logo_guid',
    );

    public function prepare()
    {
        if (!$this->tableLoaded) {
            $this->funcTable = new SymbolTable(array_flip($this->funcTable), SymbolTable::IC);
            $this->tableLoaded = true;
        }
    }

    public function leaveNode($node)
    {
        // Function call
        if ($this->isRemovedFunc($node)) {
            /**
             * {Description}
             * PHP logo GUIDs removed
             * The GUIDs that previously resulted in PHP outputting various
             * logos have been removed. This includes the removal of the
             * functions to return those GUIDs. 
             *
             * {Reference}
             * http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.guid
             */
            $this->addSpot('FATAL', sprintf('Function %s() is removed', $node->name));
        }
    }

    public function isRemovedFunc($node)
    {
        return ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name));
    }
}
