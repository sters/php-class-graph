<?php

namespace ClassGraph;

use PhpParser\Node;
use PhpParser\Node\Expr;
use PhpParser\Node\Stmt;
use PhpParser\NodeVisitor;
use PhpParser\NodeVisitorAbstract;

/**
 * Visitor is implemented NodeVisitor for dependency finding
 */
class Visitor extends NodeVisitorAbstract implements NodeVisitor
{
    protected $namespace = '';
    protected $class = '';
    protected $uses = [];
    protected $skip = [
        'self', 'static', 'parent',
    ];

    /** @var \Closure[] function(Node $node) */
    protected $hooks = [];

    public function enterNode(Node $node)
    {
        // namespace Foo;
        if ($node instanceof Stmt\Namespace_ && $this->namespace === '' && !empty($node->name->parts)) {
            $this->namespace = implode('\\', $node->name->parts);
        }

        // class Foo extends Bar implements Hoge; => Bar, Hoge
        // trait Foo {}
        if (($node instanceof Stmt\Class_ || $node instanceof Stmt\Trait_) && $this->class === '') {
            $this->class = $node->name;
            if (!is_string($node->name)) {
                $this->class = $node->name->name;
            }

            // class Foo extends Bar; => Bar
            if (!is_null($node->extends)) {
                $this->addUsesForNameParts($node->extends);
            }
            // class Foo implements Hoge; => Hoge
            if (!empty($node->implements)) {
                $this->addUsesForNameParts($node->implements);
            }
        }

        // use Foo\Bar as Hoge; => Foo\Bar
        if ($node instanceof Stmt\UseUse) {
            if (!empty($node->alias)) {
                $this->skip[] = $node->alias->name;
            }
            $this->addUsesForNameParts($node->name, true);
            $this->skip[] = implode('\\', $node->name->parts);
            $this->skip[] = $node->name->parts[count($node->name->parts) - 1];
            $this->skip = array_unique($this->skip);
        }

        // use Foo\Bar; (trait)
        if ($node instanceof Stmt\TraitUse) {
            foreach ($node->traits as $t) {
                $this->addUsesForNameParts($t);
            }
        }

        // Foo\Bar::HOGE => Foo\Bar
        if ($node instanceof Expr\ClassConstFetch) {
            $this->addUsesForNameParts($node->class);
        }

        // Foo\Bar::id => Foo\Bar
        if ($node instanceof Expr\StaticPropertyFetch) {
            $this->addUsesForNameParts($node->class);
        }

        // Foo\Bar::get(); => Foo\Bar
        if ($node instanceof Expr\StaticCall) {
            $this->addUsesForNameParts($node->class);
        }

        // new Foo(); => Foo
        if ($node instanceof Expr\New_) {
            $this->addUsesForNameParts($node->class);
        }

        foreach ($this->hooks as $hook) {
            $hook($node);
        }
    }

    private function addUsesForNameParts($parts, $raw = false)
    {
        if (empty($parts)) {
            return;
        }

        if (is_string($parts)) {
            $this->uses[] = $parts;
            $this->uses = array_unique($this->uses);
            return;
        }

        if (is_object($parts)) {
            if (!empty($parts->parts)) {
                $p = $parts->parts;
                if ($parts instanceof Node\Name\FullyQualified) {
                    array_unshift($p, '');
                }
                $this->addUsesForNameParts($p, $raw);
            }
            return;
        }

        $className = $parts;
        if (is_array($parts)) {
            $className = implode('\\', $parts);
        }

        if ($className[0] === '\\' || $raw) {
            $this->uses[] = $className;
            $this->uses = array_unique($this->uses);
            return;
        }
        if (in_array($className, $this->skip)) {
            return;
        }

        $name = '';
        if (!empty($this->namespace) && !$raw) {
            $found = false;
            foreach ($this->skip as $skip) {
                if ($skip === $className) {
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $name .= $this->namespace . '\\';
            }
        }

        $this->uses[] = $name . $className;
        $this->uses = array_unique($this->uses);
    }

    /**
     * @return string|null
     */
    public function getClass(): ?string
    {
        return $this->class;
    }

    /**
     * @return string|null
     */
    public function getNamespace(): ?string
    {
        return $this->namespace;
    }

    /**
     * @return array
     */
    public function getUses(): array
    {
        return $this->uses;
    }

    /**
     * @return string
     */
    public function getFullClassName(): string
    {
        $ns = $this->getNamespace();
        if (!empty($ns)) {
            $ns = $ns . '\\';
        } else {
            $ns = '';
        }
        return $ns . $this->getClass();
    }

    /**
     * @param $f \Closure function(Node $node)
     */
    public function addHook(\Closure $f)
    {
        $this->hooks[] = \Closure::bind($f, $this, get_class());
    }
}
