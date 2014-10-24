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

class IncompatibleToStringWithArg extends Change
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
The __toString() magic method can no longer accept arguments.
EOT;

    protected $errmsg = <<<EOT
Fatal error:  Method Test::__tostring() cannot take arguments in %file% on line %line%
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.incompatible.php';

    protected $cur_file;

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;  // TODO: 统一保存
    }

    public function leaveNode($node)
    {
        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            foreach ($node->getMethods() as $mnode) {
                if ($mnode->name == '__toString' && count($mnode->params) != 0) {
                    $errmsg = str_replace(
                        array('%name%', '%file%', '%line%'),
                        array($mnode->name, $this->cur_file, $node->getLine()),
                        $this->errmsg
                    );
                    echo $errmsg."\n";
                }
            }
        }
    }
}

