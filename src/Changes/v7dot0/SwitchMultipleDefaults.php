<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Stmt;

class SwitchMultipleDefaults extends AbstractChange
{
    protected static $version = '7.0.0';

    protected $currentSwitch; // TODO move to CheckVisitor

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
                $this->addSpot('WARNING', true, 'Switch statements cannot have multiple default blocks');
            } else {
                $this->hasDefault = true;
            }
        } elseif ($node instanceof Stmt\Switch_) {
            $this->currentSwitch = null;
        }
    }
}
