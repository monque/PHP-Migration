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
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class IncompHashAlgo extends AbstractChange
{
    protected static $version = '5.4.0';

    protected $funcTable = array(
        'hash',
        'hash_file',
        'hash_hmac',
        'hash_hmac_file',
        'hash_init',
    );

    public function __construct()
    {
        $this->funcTable = new SymbolTable($this->funcTable, SymbolTable::IC);
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * The Salsa10 and Salsa20 hash algorithms have been removed.
         *
         * {Reference}
         * http://php.net/manual/en/migration54.incompatible.php
         */

        if ($node instanceof Expr\FuncCall && $this->funcTable->has($node->name)) {
            $affected = true;
            $certain = false;

            if (!isset($node->args[0])) {
                return;
            }
            $param = $node->args[0]->value;

            if ($param instanceof Scalar\String_) {
                $certain = $affected = (strcasecmp($param->value, 'salsa10') === 0 ||
                    strcasecmp($param->value, 'salsa20') === 0);
            }

            if ($affected) {
                $this->addSpot('WARNING', $certain, 'Salsa10 and Salsa20 hash algorithms have been removed');
            }
        }
    }
}
