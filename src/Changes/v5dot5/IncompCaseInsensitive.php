<?php

namespace PhpMigration\Changes\v5dot5;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;
use PhpParser\Node\Name;

class IncompCaseInsensitive extends AbstractChange
{
    protected static $version = '5.5.0';

    protected $keywords = [
        'self'      => 'self',
        'parent'    => 'parent',
        'static'    => 'static',
    ];

    public function __construct()
    {
        $this->keywords = new SymbolTable($this->keywords, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * self, parent and static are now always case insensitive
         * Prior to PHP 5.5, cases existed where the self, parent, and static
         * keywords were treated in a case sensitive fashion. These have now
         * been resolved, and these keywords are always handled case
         * insensitively: SELF::CONSTANT is now treated identically to
         * self::CONSTANT.
         *
         * {Reference}
         * http://php.net/manual/en/migration55.incompatible.php#migration55.incompatible.self-parent-static
         */
        if (($node instanceof Expr\StaticCall || $node instanceof Expr\StaticPropertyFetch)
                && $node->class instanceof Name) {
            $name = $node->class->toString();
            if ($this->keywords->has($name) && $this->keywords->get($name) != $name) {
                $this->addSpot(
                    'NOTICE',
                    true,
                    $name.' will be case insensitive, treated identically to '.strtolower($name)
                );
            }
        }
    }
}
