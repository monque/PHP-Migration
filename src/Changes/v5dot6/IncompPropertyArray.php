<?php
namespace PhpMigration\Changes\v5dot6;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node\Scalar;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class IncompPropertyArray extends AbstractChange
{
    protected static $version = '5.6.0';

    public function leaveNode($node)
    {
        /**
         * {Description}
         * Array keys won't be overwritten when defining an array as a property
         * of a class via an array literal
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
            $array_list = array();
            $const_table = array();

            // Gather all array property, save class const
            foreach ($node->stmts as $stmt) {
                if ($stmt instanceof Stmt\Property) {
                    foreach ($stmt->props as $prop) {
                        if ($prop instanceof Stmt\PropertyProperty &&
                                $prop->default instanceof Expr\Array_) {
                            $array_list[] = $prop->default;
                        }
                    }
                } elseif ($stmt instanceof Stmt\ClassConst) {
                    foreach ($stmt->consts as $const) {
                        if ($const->value instanceof Scalar) {
                            $const_table['self::'.$const->name] = $const->value->value;
                        }
                    }
                }
            }

            // Check keys in array
            foreach ($array_list as $arr) {
                // Emulate array key initialization
                $keylist = array();
                $has = array(
                    'scalar'    => false,
                    'const'     => false,
                    'null'      => false,
                    'unfetched' => false,
                );
                $counter = 0;
                foreach ($arr->items as $item) {
                    if ($item->key instanceof Expr\ClassConstFetch) {
                        $has['const'] = true;
                        // Try to fetch const value
                        $const_name = $item->key->class.'::'.$item->key->name;
                        if (isset($const_table[$const_name])) {
                            $keylist[] = 'V:'.$const_table[$const_name];
                        } else {
                            $has['unfetched'] = true;
                            $keylist[] = 'C:'.$const_name;
                        }
                    } elseif ($item->key instanceof Expr\ConstFetch) {
                        $has['const'] = true;
                        $has['unfetched'] = true;
                        $keylist[] = 'C:'.$item->key->name->toString();
                    } elseif (is_null($item->key)) {
                        $has['null'] = true;
                        $keylist[] = 'N:'.$counter++;
                    } else {
                        $has['scalar'] = true;
                        $keylist[] = 'V:'.$item->key->value;
                    }
                }
                $has['duplicated'] = count($keylist) != count(array_unique($keylist));

                // Check condition
                if ($has['const'] && ($has['null'] || $has['unfetched'] || $has['duplicated'])) {
                    $this->addSpot(
                        'WARNING',
                        false,
                        'Array key may be overwritten when defining as a property and using constants',
                        $arr->getLine()
                    );
                }
            }
        }
    }
}
