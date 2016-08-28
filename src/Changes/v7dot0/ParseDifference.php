<?php

namespace PhpMigration\Changes\v7dot0;

use PhpMigration\Changes\AbstractChange;
use PhpParser\Error as PhpParserError;
use PhpParser\Node;
use PhpParser\ParserFactory;

/**
 * File will be parsed as PHP 5 after traverser, and checked if any difference
 * between these two results.
 *
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.variable-handling.indirect
 * @see http://php.net/manual/en/migration70.incompatible.php#migration70.incompatible.other.yield
 */
class ParseDifference extends AbstractChange
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

    /**
     * Normalize node name and convert NameResolver's FullyQualified node
     */
    protected function normalizeNodeList(array &$nodes)
    {
        foreach ($nodes as &$node) {
            if ($node == 'PhpParser\Node\Name\FullyQualified') {
                $node = 'Node\Name';
            } else {
                $node = substr($node, 10); // Strip namespace prefix
            }
        }
    }

    public function __construct()
    {
        $this->parser5 = (new ParserFactory)->create(ParserFactory::ONLY_PHP5);
    }

    public function beforeTraverse(array $nodes)
    {
        $this->lines = $this->plain5 = $this->plain7 = [];
    }

    public function enterNode($node)
    {
        $this->lines[] = $node->getLine();
        $this->plain7[] = get_class($node);
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
        $this->normalizeNodeList($this->plain5);
        $this->normalizeNodeList($this->plain7);
        $diff = array_diff_assoc($this->plain5, $this->plain7);

        $lset = [];
        foreach ($diff as $i => $name) {
            // TODO we do like double-? in PHP 7 such as `$line = $this->lines[$i] ?? 0;`
            $line = isset($this->lines[$i]) ? $this->lines[$i] : 0;
            if (isset($lset[$line])) {
                continue;
            }
            $lset[$line] = true;

            $this->addSpot('WARNING', true, 'Different behavior between PHP 5/7', $line);
        }
    }
}
