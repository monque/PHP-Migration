<?php

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class ChangeDev extends Change
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
The behaviour of functions with by-reference parameters called by value has
changed. Where previously the function would accept the by-value argument, a
fatal error is now emitted. Any previous code passing constants or literals to
functions expecting references, will need altering to assign the value to a
variable before calling the function.
EOT;

    protected $errmsg = <<<EOT
Fatal error: Only variables can be passed by reference in %file% on line %line%
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.incompatible.php';

    protected static $tb_def;

    protected static $tb_defm;

    protected static $tb_call;

    protected $cur_file;

    protected $cur_class;

    public function prepare()
    {
        // TODO: Convert to full qulified name first

        self::$tb_def = array();
        self::$tb_defm = array();
        self::$tb_call = array();

        // Load build-in functions
        $lines = file(__dir__.'/buildin.dat');
        foreach ($lines as $line) {
            list($fname, $posbit) = explode(' ', $line, 2);
            self::$tb_def[$fname] = array(
                'pos' => intval($posbit),
                'line' => null,
            );
        }
    }

    public function finish()
    {
        // Dump table
        // foreach (self::$tb_def as $name => $info) {
        //     printf("function %s(%b)\n", $name, $info['pos']);
        // }
        // foreach (self::$tb_call as $info) {
        //     printf("call %s(%b)\n", $info['name'], $info['pos']);
        // }

        // Check all call
        foreach (self::$tb_call as $cinfo) {
            $cname = $cinfo['name'];
            if (isset(self::$tb_def[$cname])) {
                if (!$this->isMismatch(self::$tb_def[$cname]['pos'], $cinfo['pos'])) {
                    continue;
                }

                $errmsg = str_replace(
                    array('%file%', '%line%'),
                    array($cinfo['file'], $cinfo['line']),
                    $this->errmsg
                );
                printf("%s. When calling `%s`\n", $errmsg, $cname);
            } elseif (substr($cname, 0, 2) == '->' && isset(self::$tb_defm[$cname])) {
                $suspected = array();
                foreach (self::$tb_defm[$cname] as $class => $dinfo) {
                    if ($this->isMismatch($dinfo['pos'], $cinfo['pos'])) {
                        $suspected[] = $class;
                    }
                }

                if ($suspected) {
                    $errmsg = str_replace(
                        array('%file%', '%line%'),
                        array($cinfo['file'], $cinfo['line']),
                        $this->errmsg
                    );
                    printf("%s. When calling `%s` with instance of `%s`\n", $errmsg, $cname, implode(', ', $suspected));
                }
            }
        }
    }

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;
        $this->cur_class = null;
    }

    public function afterTraverse($filename)
    {
    }

    public function enterNode($node)
    {
        // Record current class name
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->cur_class = $node->name;
        }
    }

    public function leaveNode($node)
    {
        // Populate
        if ($node instanceof PhpParser\Node\Stmt\Function_) {
            $this->populateDefine($node, 'func');
        } elseif ($node instanceof PhpParser\Node\Stmt\ClassMethod) {
            $this->populateDefine($node, 'method');
        } elseif ($node instanceof PhpParser\Node\Expr\FuncCall) {
            $this->populateCall($node, 'func');
        } elseif ($node instanceof PhpParser\Node\Expr\StaticCall) {
            $this->populateCall($node, 'static');
        } elseif ($node instanceof PhpParser\Node\Expr\MethodCall) {
            $this->populateCall($node, 'method');
        }

        // Clear current class name
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            $this->cur_class = null;
        }
    }

    protected function getPositionsWithRef($node)
    {
        $posbit = 0;
        foreach ($node->params as $pos => $param) {
            if ($param->byRef == 1) {
                $posbit |= 1 << $pos;
            }
        }
        return $posbit;
    }

    protected function getPositionsNoneVar($node)
    {
        $posbit = 0;
        foreach ($node->args as $pos => $arg) {
            if ($arg->value instanceof PhpParser\Node\Expr\Variable ||
                $arg->value instanceof PhpParser\Node\Expr\PropertyFetch ||
                $arg->value instanceof PhpParser\Node\Expr\ArrayDimFetch ||
                $arg->value instanceof PhpParser\Node\Expr\FuncCall) {
                continue;
            } elseif ($arg->value instanceof PhpParser\Node\Expr\Assign) {
                // Variable in assign expression
                if ($arg->value->var instanceof PhpParser\Node\Expr\Variable) {
                    continue;
                }
            }
            $posbit |= 1 << $pos;
        }
        return $posbit;
    }

    protected function isMismatch($define, $call)
    {
        return true && $define & $call;
    }

    protected function populateDefine($node, $type)
    {
        $posbit = $this->getPositionsWithRef($node);
        if (!$posbit) {
            return;
        }

        $dinfo = array(
            'pos' => $posbit,
            'line' => $node->getLine(),
        );

        if ($type == 'func') {
            $fname = $node->name;
        } elseif ($node->isStatic()) {
            $fname = $this->cur_class.'::'.$node->name;
        } else {
            $fname = $this->cur_class.'->'.$node->name;
            self::$tb_defm['->'.$node->name][$this->cur_class] = $dinfo;
        }

        self::$tb_def[$fname] = $dinfo;
    }

    protected function dynamicCallname($node)
    {
        /**
         * Due to the mechanism of dynamic script programming language,
         * it's TOO hard to guess what the callname exactly references to.
         * eg: $_GET['func']($arg)
         * So we do nothing instead of guessing.
         */
    }

    protected function populateCall($node, $type)
    {
        if (!is_string($node->name) && !($node->name instanceof PhpParser\Node\Name)) {
            return $this->dynamicCallname($node);
        }

        $posbit = $this->getPositionsNoneVar($node);
        if (!$posbit) {
            return;
        }

        if ($type == 'func') {
            $callname = (string) $node->name;
        } elseif ($type == 'static') {
            $class = $node->class->toString();
            if ($class == 'self' && !is_null($this->cur_class)) {
                $class = $this->cur_class;
            }

            $callname = $class.'::'.$node->name;
        } elseif ($type == 'method') {
            $object = $node->var->name;
            if ($object == 'this' && !is_null($this->cur_class)) {
                $object = $this->cur_class;
            } else {
                $object = '';
            }
            $callname = $object.'->'.$node->name;
        }

        self::$tb_call[] = array(
            'name' => $callname,
            'pos' => $posbit,
            'file' => $this->cur_file,
            'line' => $node->getLine(),
        );
    }
}
