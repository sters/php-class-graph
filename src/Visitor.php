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
    private const NamespaceSeparator = '\\';

    protected $namespace = '';
    protected $class = '';
    protected $uses = [];
    protected $skip = ['self', 'static', 'parent'];
    protected $importedNamespaces = [];

    /** @var \Closure[] function(Node $node) */
    protected $hooks = [];

    public function enterNode(Node $node)
    {
        // namespace Foo;
        if ($node instanceof Stmt\Namespace_ && $this->namespace === '' && !empty($node->name->parts)) {
            $this->namespace = self::NamespaceSeparator . implode(self::NamespaceSeparator, $node->name->parts);
            return;
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
            return;
        }

        // use Foo\Bar as Hoge; => Foo\Bar (imports)
        if ($node instanceof Node\Stmt\Use_) {
            $trim = false;
            if ($node->type === Node\Stmt\Use_::TYPE_FUNCTION || $node->type === Node\Stmt\Use_::TYPE_CONSTANT) {
                $trim = true;
            }

            foreach ($node->uses as $use) {
                $parts = $use->name->parts;
                if ($trim) {
                    array_pop($parts);
                }
                $name = implode(self::NamespaceSeparator, $parts);
                if ($name[0] !== self::NamespaceSeparator) {
                    $name = self::NamespaceSeparator . $name;
                }

                $this->addUsesForNameParts($name, true);

                $alias = $parts[count($parts) - 1];
                if (!empty($use->alias)) {
                    $alias = $use->alias->name;
                }
                $this->importedNamespaces[$alias] = $name;
            }

            return;
        }

        // use Foo\Bar; (trait)
        if ($node instanceof Stmt\TraitUse) {
            foreach ($node->traits as $t) {
                $this->addUsesForNameParts($t);
            }
            return;
        }

        // Foo\Bar::HOGE => Foo\Bar
        if ($node instanceof Expr\ClassConstFetch) {
            $this->addUsesForNameParts($node->class);
            return;
        }

        // Foo\Bar::id => Foo\Bar
        if ($node instanceof Expr\StaticPropertyFetch) {
            $this->addUsesForNameParts($node->class);
            return;
        }

        // Foo\Bar::get(); => Foo\Bar
        if ($node instanceof Expr\StaticCall) {
            $this->addUsesForNameParts($node->class);
            return;
        }

        // new Foo(); => Foo
        if ($node instanceof Expr\New_) {
            $this->addUsesForNameParts($node->class);
            return;
        }

        foreach ($this->hooks as $hook) {
            $hook($node);
        }
    }

    private function resolveNameParts($parts): string
    {
        if (empty($parts)) {
            return '';
        }

        if (is_object($parts)) {
            if (!empty($parts->parts)) {
                $p = $parts->parts;
                if ($parts instanceof Node\Name\FullyQualified) {
                    array_unshift($p, '');
                }
                return $this->resolveNameParts($p);
            }

            return '';
        }

        if (is_array($parts)) {
            return $this->resolveNameParts(implode(self::NamespaceSeparator, $parts));
        }

        if ($parts === '\\Array') {
            throw new \Exception;
        }

        if (is_string($parts)) {
            // full
            if ($parts[0] == self::NamespaceSeparator) {
                return $parts;
            }

            // relative
            $partsArr = explode(self::NamespaceSeparator, $parts);
            $part0 = array_shift($partsArr);
            foreach ($this->importedNamespaces as $alias => $actual) {
                if ($alias === $part0) {
                    if (empty($partsArr)) {
                        return $actual;
                    }
                    return $actual . self::NamespaceSeparator . implode(self::NamespaceSeparator, $partsArr);
                }
            }

            return $this->namespace . self::NamespaceSeparator . $parts;
        }

        // unexpected
        return '';
    }

    private function addUsesForNameParts($parts, $raw = false)
    {
        $className = $this->resolveNameParts($parts);
        if ($parts === '') {
            return;
        }

        if (in_array($className, $this->skip)) {
            return;
        }

        $this->uses[] = $className;
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
            $ns = $ns . self::NamespaceSeparator;
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
