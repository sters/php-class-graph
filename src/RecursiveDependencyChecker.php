<?php

namespace ClassGraph;

/**
 * RecursiveDependencyChecker do Traverser with new blank DependencyList, from SourceList
 */
class RecursiveDependencyChecker
{
    protected $sourceList;
    protected $dependencyList;
    protected $traverser;

    public function __construct(SourceList $startSourceList)
    {
        $this->sourceList = clone $startSourceList;
        $this->dependencyList = new DependencyList;
        $this->traverser = new Traverser;
    }

    public function run()
    {
        foreach ($this->sourceList as $target) {
            $visitor = $this->traverser->traverse($target);
            $base = new Dependency($visitor->getFullClassName());
            $base->setFileName($target);
            foreach ($visitor->getUses() as $u) {
                $this->dependencyList->addDependency($base, new Dependency($u));
                $this->sourceList->add($u);
            }
        }
    }

    public function getDependencyList(): DependencyList
    {
        return $this->dependencyList;
    }

    public function getTraverser(): Traverser
    {
        return $this->traverser;
    }

    public function setTraverser(Traverser $t)
    {
        $this->traverser = $t;
    }
}
