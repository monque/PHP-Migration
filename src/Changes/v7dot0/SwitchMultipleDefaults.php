<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Stmt;

/**
 * Switch statements cannot have multiple default blocks
 *
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.multiple-default
 */
class SwitchMultipleDefaults extends AbstractChange
{
    protected static $version = '7.0.0';

    /**
     * Record current switch block only in this file. Do not move to
     * CheckVisitor if no another need.
     */
    protected $currentSwitch;

    protected $hasDefault;

    public function enterNode($node)
    {
        if ($node instanceof Stmt\Switch_) {
            $this->currentSwitch = $node;
            $this->hasDefault = false;
        }
    }

    public function leaveNode($node)
    {
        if ($node instanceof Stmt\Case_ && !is_null($this->currentSwitch) && is_null($node->cond)) {
            if ($this->hasDefault) {
                $this->addSpot('FATAL', true, 'Switch statements cannot have multiple default blocks');
            } else {
                $this->hasDefault = true;
            }
        } elseif ($node instanceof Stmt\Switch_) {
            $this->currentSwitch = null;
        }
    }
}
