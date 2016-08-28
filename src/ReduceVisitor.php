<?php

namespace PhpMigration;

use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\NodeVisitorAbstract;

class ReduceVisitor extends NodeVisitorAbstract
{
    public function beforeTraverse(array $nodes)
    {
    }

    public function enterNode(Node $node)
    {
        // Aggregate namespacedName & name
        if (property_exists($node, 'namespacedName')) {
            $node->migName = $this->getName($node->namespacedName);
        } elseif (property_exists($node, 'name')) {
            $node->migName = $this->getName($node->name);
        }

        // Extends name
        if (property_exists($node, 'extends')) {
            $node->migExtends = $this->getName($node->extends);
        }
    }

    public function leaveNode(Node $node)
    {
    }

    public function afterTraverse(array $nodes)
    {
    }

    /**
     * Get data from name node or pure string
     */
    protected function getName($name)
    {
        return $name instanceof Name ? $name->toString() : $name;
    }
}
