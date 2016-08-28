<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;

/**
 * Functions cannot have multiple parameters with the same name
 *
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.func-parameters
 */
class FuncParameters extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        if (!isset($node->params)) {
            return;
        }

        $set = [];
        foreach ($node->params as $param) {
            if (isset($set[$param->name])) {
                $this->addSpot('WARNING', true, 'Can not define two or more parameters with the same name');
                break;
            }
            $set[$param->name] = true;
        }
    }
}
