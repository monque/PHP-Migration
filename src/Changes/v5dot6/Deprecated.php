<?php
namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChange;
use PhpMigration\SymbolTable;
use PhpParser\Node\Expr;
use PhpParser\Node\Scalar;

class Deprecated extends AbstractChange
{
    protected static $version = '5.6.0';

    protected $checkHRPD = true;

    /**
     * For another Changes to set whether skip this check
     */
    public function skipHRPD($off)
    {
        $this->checkHRPD = !$off;
    }

    public function leaveNode($node)
    {
        /**
         * {Description}
         * always_populate_raw_post_data will now generate an E_DEPRECATED
         * error when used. New code should use php://input instead of
         * $HTTP_RAW_POST_DATA, which will be removed in a future release. You
         * can opt in for the new behaviour (in which $HTTP_RAW_POST_DATA is
         * never defined) by setting always_populate_raw_post_data to -1.
         *
         * {Reference}
         * http://php.net/manual/en/migration56.deprecated.php#migration56.deprecated.raw-post-data
         */
        if ($this->checkHRPD && $node instanceof Expr\Variable && !($node->name instanceof Expr\Variable) &&
                $node->name == 'HTTP_RAW_POST_DATA') {
            $this->addSpot('DEPRECATED', true, '$HTTP_RAW_POST_DATA is deprecated, use php://input instead');
        }
    }
}
