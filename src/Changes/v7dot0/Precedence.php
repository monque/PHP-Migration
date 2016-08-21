<?php
namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Error as PhpParserError;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\ParserFactory;

class Precedence extends AbstractChange
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
        if ($node instanceof Name\FullyQualified) {
            $this->plain7[] = 'PhpParser\Node\Name';
        } else {
            $this->plain7[] = get_class($node);
        }
    }

    public function afterTraverse(array $nodes)
    {
        // Parse code as PHP 5
        try {
            $stmts = $this->parser5->parse($this->visitor->getCode());
        } catch (PhpParserError $e) {
            $this->addSpot('WARNING', true, 'Parse failed as PHP 5 "'.$e->getMessage().'"', $e->getStartLine());
            return;
        }

        // Compare
        $this->ast2plain($stmts, $this->plain5);
        $diff = array_diff_assoc($this->plain5, $this->plain7);

        $lset = array();
        foreach ($diff as $i => $name) {
            $line = $this->lines[$i];
            if (isset($lset[$line])) {
                continue;
            }
            $lset[$line] = true;

            $this->addSpot('WARNING', true, 'Changing of evaluation precedence affects',
                $this->lines[$i], $this->visitor->getFile());
        }
    }
}
