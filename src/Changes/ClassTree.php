<?php
namespace PhpMigration\Changes;

/**
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpMigration\Changes\AbstractChange;
use PhpMigration\Utils\Logging;
use PhpParser\Node\Stmt;

class ClassTree extends AbstractChange
{
    protected $classTable;

    public function prepare()
    {
        $this->classTable = array();
    }

    public function leaveNode($node)
    {
        if ($node instanceof Stmt\Class_) {
            $name = $node->namespacedName->toString();
            $parent_name = is_null($node->extends) ? null : $node->extends->toString();

            if (isset($this->classTable[$name])) {
                Logging::notice('Found a duplicated class '.$name.' in '.$this->visitor->getFile());
            }

            $this->classTable[$name] = array(
                'parent' => $parent_name,
                'children' => array(),
                'topentry' => true,
            );
        }
    }

    public function finish()
    {
        // Find parent
        foreach ($this->classTable as $name => &$self) {
            if (is_null($self['parent'])) {
                continue;
            }
            $parent_name = $self['parent'];
            if (!isset($this->classTable[$parent_name])) {
                continue;
            }

            $self['topentry'] = false;
            $this->classTable[$parent_name]['children'][$name] = &$self;
        }

        // Output
        if ($this->classTable) {
            $this->outputTree($this->classTable);
        } else {
            echo "No class found\n";
        }
    }

    protected function outputTree($data, $depth = 0, $last_status = array())
    {
        if (!is_array($data) || empty($data)) {
            return;
        }

        ksort($data);

        // Record last name
        foreach ($data as $name => $node) {
            if ($depth == 0 && !$node['topentry']) {
                continue;
            }
            $lastname = $name;
        }

        foreach ($data as $name => $node) {
            if ($depth == 0 && !$node['topentry']) {
                continue;
            }
            $is_last = ($name == $lastname);

            // Padding
            $padding = '';
            for ($i = 0; $i < $depth; $i++) {
                if ($last_status[$i]) {
                    $padding .= '    ';
                } else {
                    $padding .= '|   ';
                }
            }
            $padding .= ($is_last ? '`' : '|').'-- ';

            // Output
            echo $padding.$name."\n";

            if ($node['children']) {
                $last_status[$depth] = $is_last;
                $this->outputTree($node['children'], $depth + 1, $last_status);
            }
        }
    }
}
