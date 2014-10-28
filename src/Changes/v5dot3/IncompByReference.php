<?php
namespace PhpMigration\Changes\v5dot3;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Change;
use PhpMigration\Utils\ParserHelper;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;

class IncompByReference extends Change
{
    protected static $tb_def;
    protected static $tb_defm;
    protected static $tb_call;

    protected function emitSpot($cinfo, $suspected = null)
    {
        /*
         * {Description}
         * The behaviour of functions with by-reference parameters called by 
         * value has changed. Where previously the function would accept the 
         * by-value argument, a fatal error is now emitted. Any previous code 
         * passing constants or literals to functions expecting references, 
         * will need altering to assign the value to a variable before calling 
         * the function. 
         *
         * {Errmsg}
         * Fatal error: Only variables can be passed by reference
         *
         * {Reference}
         * http://php.net/manual/en/migration53.incompatible.php
         */

        if ($suspected) {
            $message = 'Only variables can be passed by reference, when %s called by instance %s';
            $message = sprintf($message, $cinfo['name'], implode(',', $suspected));
        } else {
            $message = 'Only variables can be passed by reference';
        }

        $this->visitor->addSpot($message, $cinfo['line'], $cinfo['file']);
    }

    protected function emitPassByRef($node)
    {
        /*
         * {Description}
         * Call-time pass-by-reference is now deprecated
         *
         * {Reference}
         * http://php.net/manual/en/migration53.deprecated.php
         */
        $this->visitor->addSpot('Calltime pass-by-reference is deprecated');
    }

    public function prepare()
    {
        // Init defination table
        self::$tb_def = array();
        self::$tb_defm = array();
        self::$tb_call = array();

        // TODO: find a better loading method
        // Load build-in functions
        if (false) {
            $lines = file(__dir__.'/buildin.dat');
            foreach ($lines as $line) {
                list($fname, $posbit) = explode(' ', $line, 2);
                self::$tb_def[$fname] = array(
                    'pos' => intval($posbit),
                    'line' => null,
                );
            }
        }
    }

    public function finish()
    {
        // Dump table
        if (false) {
            foreach (self::$tb_def as $name => $posbit) {
                printf("function %s(%b)\n", $name, $posbit);
            }
            foreach (self::$tb_call as $info) {
                printf("call %s(%b)\n", $info['name'], $info['pos']);
            }
        }

        // Check all call
        foreach (self::$tb_call as $cinfo) {
            $cname = $cinfo['name'];
            if (isset(self::$tb_def[$cname])) {
                if ($this->isMismatch(self::$tb_def[$cname], $cinfo['pos'])) {
                    $this->emitSpot($cinfo);
                }
            } elseif (substr($cname, 0, 2) == '->' && isset(self::$tb_defm[$cname])) {
                $suspected = array();
                foreach (self::$tb_defm[$cname] as $class => $posbit) {
                    if ($this->isMismatch($posbit, $cinfo['pos'])) {
                        $suspected[] = $class;
                    }
                }
                if ($suspected) {
                    $this->emitSpot($cinfo, $suspected);
                }
            }
        }
    }

    public function leaveNode($node)
    {
        // Populate
        if ($node instanceof Stmt\Function_) {
            $this->populateDefine($node, 'func');
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->populateDefine($node, 'method');
        } elseif ($node instanceof Expr\FuncCall) {
            $this->populateCall($node, 'func');
        } elseif ($node instanceof Expr\StaticCall) {
            $this->populateCall($node, 'static');
        } elseif ($node instanceof Expr\MethodCall) {
            $this->populateCall($node, 'method');
        }
    }

    protected function positionWithRef($node)
    {
        $posbit = 0;
        foreach ($node->params as $pos => $param) {
            if ($param->byRef == 1) {
                $posbit |= 1 << $pos;
            }
        }
        return $posbit;
    }

    protected function positionByValue($node)
    {
        $posbit = 0;
        foreach ($node->args as $pos => $arg) {
            if ($arg->value instanceof Expr\Variable ||
                $arg->value instanceof Expr\PropertyFetch ||
                $arg->value instanceof Expr\ArrayDimFetch ||
                $arg->value instanceof Expr\FuncCall) {
                continue;
            } elseif ($arg->value instanceof Expr\Assign) {
                // Variable in assign expression
                if ($arg->value->var instanceof Expr\Variable) {
                    continue;
                }
            }
            $posbit |= 1 << $pos;
        }
        return $posbit;
    }

    protected function checkPassByRef($node)
    {
        foreach ($node->args as $arg) {
            if ($arg->byRef && $arg->value instanceof Expr\Variable) {
                return $this->emitPassByRef($node);
            }
        }
    }

    protected function isMismatch($define, $call)
    {
        return true && ($define & $call);
    }

    protected function populateDefine($node, $type)
    {
        $posbit = $this->positionWithRef($node);
        if (!$posbit) {
            return;
        }

        if ($type == 'func') {
            $fname = $node->name;
        } elseif ($node->isStatic()) {
            $fname = $this->visitor->getClassname().'::'.$node->name;
        } else {
            $fname = $this->visitor->getClassname().'->'.$node->name;
            self::$tb_defm['->'.$node->name][$this->visitor->getClassname()] = $posbit;
        }

        self::$tb_def[$fname] = $posbit;
    }

    protected function populateCall($node, $type)
    {
        $this->checkPassByRef($node);

        if (ParserHelper::isDynamicCall($node)) {
            return;
        }

        $posbit = $this->positionByValue($node);
        if (!$posbit) {
            return;
        }

        if ($type == 'func') {
            $callname = (string) $node->name;
        } elseif ($type == 'static') {
            $class = $node->class->toString();
            if ($class == 'self' && $this->visitor->inClass()) {
                $class = $this->visitor->getClassname();
            }
            $callname = $class.'::'.$node->name;
        } elseif ($type == 'method') {
            $object = $node->var->name;
            if ($object == 'this' && $this->visitor->inClass()) {
                $object = $this->visitor->getClassname();
            } else {
                $object = '';
            }
            $callname = $object.'->'.$node->name;
        }

        self::$tb_call[] = array(
            'name' => $callname,
            'pos' => $posbit,
            'file' => $this->visitor->getFile(),
            'line' => $node->getLine(),
        );
    }
}
