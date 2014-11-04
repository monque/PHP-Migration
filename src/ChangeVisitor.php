<?php
namespace PhpMigration;

/*
 * @author Yuchen Wang <phobosw@gmail.com>
 *
 * Code is compliant with PSR-1 and PSR-2 standards
 * http://www.php-fig.org/psr/psr-1/
 * http://www.php-fig.org/psr/psr-2/
 */

use PhpParser\Node;
use PhpParser\NodeVisitorAbstract;
use PhpParser\Node\Stmt;

class ChangeVisitor extends NodeVisitorAbstract
{
    protected $spots;

    protected $changes;

    protected $file;

    protected $class;

    protected $method;

    protected $function;

    protected $node;

    public function __construct($changes = array())
    {
        $this->spots = array();
        $this->changes = $changes;
        $this->filename = $this->class = $this->method = $this->function = null;
    }

    public function setFile(\SplFileInfo $file)
    {
        $this->file = $file;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getClassname()
    {
        return is_null($this->class) ? null : $this->class->name;
    }

    public function inClass()
    {
        return !is_null($this->class);
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function inMethod()
    {
        return !is_null($this->method);
    }

    public function getFunction()
    {
        return $this->function;
    }

    public function inFunction()
    {
        return !is_null($this->function);
    }

    public function prepare()
    {
        foreach ($this->changes as $change) {
            $change->setVisitor($this);
            $change->prepare();
        }
    }

    public function beforeTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->beforeTraverse();
        }
    }

    public function enterNode(Node $node)
    {
        $this->node = $node;

        // Record current
        if ($node instanceof Stmt\Class_ ||
            $node instanceof Stmt\Interface_ ||
            $node instanceof Stmt\Trait_) {  // FIXME: should Class, Interface, Trait all assign to $this->class ?
            $this->class = $node;
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->method = $node;
        } elseif ($node instanceof Stmt\Function_) {
            $this->function = $node;
        }

        foreach ($this->changes as $change) {
            $change->enterNode($node);
        }

        $this->node = null;
    }

    public function leaveNode(Node $node)
    {
        $this->node = $node;

        foreach ($this->changes as $change) {
            $change->leaveNode($node);
        }

        // Clear current
        if ($node instanceof Stmt\Class_) {
            $this->class = null;
        } elseif ($node instanceof Stmt\ClassMethod) {
            $this->method = null;
        } elseif ($node instanceof Stmt\Function_) {
            $this->function = null;
        }

        $this->node = null;
    }

    public function afterTraverse(array $nodes)
    {
        foreach ($this->changes as $change) {
            $change->afterTraverse();
        }
    }

    public function finish()
    {
        foreach ($this->changes as $change) {
            $change->finish();
        }
    }

    public function addSpot($message, $line = null, $file = null)
    {
        if (is_null($line) && $this->node instanceof Node) {
            $line = $this->node->getLine();
        }

        if (is_null($file)) {
            $file = $this->getFile();
        }
        $filename = $file->getRealpath();

        // Add by file
        $this->spots[$filename][] = array(
            'message' => $message,
            'line' => $line,
            'file' => $file,
        );
    }

    public function getSpots()
    {
        return $this->spots;
    }
}
