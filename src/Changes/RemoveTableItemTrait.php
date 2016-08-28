<?php

namespace PhpMigration\Changes;

use PhpMigration\SymbolTable;

trait RemoveTableItemTrait
{
    public function removeTableItems($name, SymbolTable $table)
    {
        foreach ($table as $item => $dummy) {
            $this->$name->del($item);
        }
    }
}
