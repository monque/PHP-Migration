<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Stmt;

/** 
 * Invalid class, interface and trait names
 *
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.classes
 */
class KeywordReserved extends AbstractChange
{
    protected static $version = '7.0.0';

    /**
     * The following names cannot be used to name classes, interfaces or
     * traits.
     */
    protected $forbiddenTable = [
        'bool', 'int', 'float', 'string', 'NULL', 'TRUE', 'FALSE',
    ];

    /**
     * Furthermore, the following names should not be used. Although they will
     * not generate an error in PHP 7.0, they are reserved for future use
     * and should be considered deprecated.
     */
    protected $reservedTable = [
        'resource', 'object', 'mixed', 'numeric',
    ];

    public function __construct()
    {
        $this->forbiddenTable = new SymbolTable($this->forbiddenTable, SymbolTable::IC);
        $this->reservedTable = new SymbolTable($this->reservedTable, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        if (!$node instanceof Stmt\ClassLike || is_null($node->name)) {
            return;
        }

        $name = $node->name;

        if ($this->forbiddenTable->has($name)) {
            $this->addSpot('FATAL', true, 'Keyword "'.$name.'" cannot be used to name class-like');
        } elseif ($this->reservedTable->has($name)) {
            $this->addSpot('NOTICE', true, 'Keyword "'.$name.'" is reserved');
        }
    }
}
