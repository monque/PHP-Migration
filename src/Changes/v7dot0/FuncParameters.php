<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Stmt;

class FuncParameters extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        if (!$node instanceof Stmt\Function_ && !$node instanceof Stmt\ClassMethod) {
            return;
        }

        $set = array();
        foreach ($node->params as $param) {
            if (isset($set[$param->name])) {
                $this->addSpot('WARNING', true, 'Functions cannot have multiple parameters with the same name');
                break;
            }
            $set[$param->name] = true;
        }
    }
}
