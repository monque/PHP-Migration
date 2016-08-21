<?php
namespace PhpMigration\Changes\v7dot0;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 */

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Scalar;

class IncompStringHandle extends AbstractChange
{
    protected static $version = '7.0.0';

    public function leaveNode($node)
    {
        if ($node instanceof Scalar\String_ &&
                preg_match('/0x[0-9a-f]+/i', $node->value) &&
                filter_var($node->value, FILTER_VALIDATE_INT, FILTER_FLAG_ALLOW_HEX)) {
            $this->addSpot('NOTICE', false, 'hexadecimal strings are no longer considered numeric');
        }
    }
}
