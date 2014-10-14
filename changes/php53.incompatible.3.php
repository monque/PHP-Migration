<?php

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code follow PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

class ChangeDev3 extends Change
{
    protected $version = '5.3.0';

    protected $description = <<<EOT
The __call() magic method is now invoked on access to private and protected methods.
EOT;

    protected $reference = 'http://php.net/manual/en/migration53.incompatible.php';

    protected $cur_file;

    public function beforeTraverse($filename)
    {
        $this->cur_file = $filename;  // TODO: 统一保存
    }

    public function leaveNode($node)
    {
        $nonpublic = array();
        $has_magic_call = false;

        if ($node instanceof PhpParser\Node\Stmt\Class_) {
            foreach ($node->getMethods() as $mnode) {
                if ($mnode->name == '__call') {
                    $has_magic_call = true;
                } elseif (!$mnode->isPublic()) {
                    $nonpublic[] = $mnode->name;
                }
            }
        }

        if ($has_magic_call && $nonpublic) {
            echo $node->name."\n";
            print_r($nonpublic);
        }
    }
}
