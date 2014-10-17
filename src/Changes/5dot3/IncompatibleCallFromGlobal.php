<?php

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class ChangeDev2 extends Change  // TODO: 起个好名字。。。
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
func_get_arg(), func_get_args() and func_num_args() can no longer be called 
from the outermost scope of a file that has been included by calling include or 
require from within a function in the calling file.
EOT;

    protected $errmsg = <<<EOT
Warning:  func_get_args():  Called from the global scope - no function context in %file% on line %line%
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.incompatible.php';

    protected $cur_file;

    protected $cur_method;

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;  // TODO: 统一保存
        $this->cur_method = null;
    }

    public function enterNode($node)
    {
        // Record
        if ($node instanceof PhpParser\Node\Stmt\Function_ ||
            $node instanceof PhpParser\Node\Stmt\ClassMethod) {
            $this->cur_method = $node->name;
        }

        // Populate
        if ($node instanceof PhpParser\Node\Expr\FuncCall) {
            if (!is_string($node->name) && !($node->name instanceof PhpParser\Node\Name)) {
                return;
            }

            $name = (string) $node->name;
            if (is_null($this->cur_method) &&
                in_array($name, array('func_get_arg', 'func_get_args', 'func_num_args'))) {

                $errmsg = str_replace(
                    array('%name', '%file%', '%line%'),
                    array($name, $this->cur_file, $node->getLine()),
                    $this->errmsg
                );
                printf("%s. When calling `%s`\n", $errmsg, $name);
            }
        }
    }

    public function leaveNode($node)
    {
        // Clear
        if ($node instanceof PhpParser\Node\Stmt\Function_ ||
            $node instanceof PhpParser\Node\Stmt\ClassMethod) {
            $this->cur_method = null;
        }
    }
}
