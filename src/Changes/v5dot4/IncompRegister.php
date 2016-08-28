<?php

namespace PhpMigration\Changes\v5dot4;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;

class IncompRegister extends AbstractChange
{
    protected static $version = '5.4.0';

    protected $longArray = [
        'HTTP_POST_VARS',
        'HTTP_GET_VARS',
        'HTTP_ENV_VARS',
        'HTTP_SERVER_VARS',
        'HTTP_COOKIE_VARS',
        'HTTP_SESSION_VARS',
        'HTTP_POST_FILES',
    ];

    public function __construct()
    {
        $this->longArray = new SymbolTable($this->longArray, SymbolTable::CS);
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The register_globals and register_long_arrays php.ini directives
         * have been removed.
         *
         * {Reference}
         * http://php.net/manual/en/migration54.incompatible.php
         */
        if ($node instanceof Expr\Variable && is_string($node->name) && $this->longArray->has($node->name)) {
            $this->addSpot(
                'WARNING',
                true,
                'The register_long_arrays is removed, $'.$node->name.' no longer available'
            );
        }
    }
}
