<?php
namespace PhpMigration\Changes\v5dot6;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class IncompPropertyArray extends Change
{
    protected static $version = '5.6.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * Array keys won't be overwritten when defining an array as a property of a class via an array literal ¶
         *
         * Previously, arrays declared as class properties which mixed explicit 
         * and implicit keys could have array elements silently overwritten if 
         * an explicit key was the same as a sequential implicit key. For 
         * example:
         *
         * {Reference}
         * http://php.net/manual/en/migration56.incompatible.php#migration56.incompatible.array-keys
         */
        if ($node instanceof Stmt\Class_) {
            $arrayList = array();

            // Gather all array property
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Stmt\Property) {
                    foreach ($stmt->props as $prop) {
                        if ($prop instanceof Stmt\PropertyProperty &&
                                $prop->default instanceof Expr\Array_) {
                            $arrayList[] = $prop->default;
                        }
                    }
                }
            }

            // Check keys in array
            foreach ($arrayList as $arr) {
                foreach ($arr->items as $item) {
                    if ($item->key instanceof Expr\ClassConstFetch) {
                        $this->addSpot('WARNING', 'Array keys won\'t be overwritten when defining as a property', $arr->getLine());
                        continue 2;
                    }
                }
            }
        }
    }
}
