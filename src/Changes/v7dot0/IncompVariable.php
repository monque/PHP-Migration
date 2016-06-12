<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Node;
use PhpParser\ParserFactory;
use PhpParser\Node\Name\FullyQualified;

class IncompVariable extends AbstractChange
{
    protected static $version = '7.0.0';

    protected $parser5;

    protected $lines;

    protected $plain5;

    protected $plain7;

    /**
     * a mini node traverser convert AST to plain list
     */
    protected function ast2plain($entry, array &$records)
    {
        if (is_array($entry)) {
            foreach ($entry as $node) {
                $this->ast2plain($node, $records);
            }
        } elseif ($entry instanceof Node) {
            $records[] = get_class($entry);
            foreach ($entry->getSubNodeNames() as $name) {
                $this->ast2plain($entry->$name, $records);
            }
        }
    }

    public function prepare()
    {
        $this->parser5 = (new ParserFactory)->create(ParserFactory::ONLY_PHP5);
    }

    public function beforeTraverse(array $nodes)
    {
        $this->lines = $this->plain5 = $this->plain7 = array();
    }

    public function enterNode($node)
    {
        $this->lines[] = $node->getLine();

        // Use normal node instead of NameResolver
        if ($node instanceof FullyQualified) {
            $this->plain7[] = 'PhpParser\Node\Name';
        } else {
            $this->plain7[] = get_class($node);
        }
    }

    public function afterTraverse(array $nodes)
    {
        // Parse code as PHP 5
        $code = file_get_contents($this->visitor->getFile());
        $stmts = $this->parser5->parse($code);
        $this->ast2plain($stmts, $this->plain5);

        // Compare
        $diff = array_diff_assoc($this->plain5, $this->plain7);
        $lset = array();
        foreach ($diff as $i => $name) {
            $line = $this->lines[$i];
            if (isset($lset[$line])) {
                continue;
            }

            $lset[$line] = true;
            $this->addSpot('WARNING', true, 'Changing of variable handling affects',
                $this->lines[$i], $this->visitor->getFile());
        }
    }
}
