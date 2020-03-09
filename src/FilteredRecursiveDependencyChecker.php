<?php

namespace ClassGraph;

use Closure;

class FilteredRecursiveDependencyChecker extends RecursiveDependencyChecker
{
    /** @var Closure function(string $target, string $baseclass, Dependency $dependency) */
    protected $filter;

    public function __construct(SourceList $startSourceList, Closure $filter)
    {
        parent::__construct($startSourceList);
        $this->filter = $filter;
    }

    public function run()
    {
        if (empty($this->filter)) {
            $this->filter = function (string $a, string $b, Dependency $c) {
                return true;
            };
        }

        foreach ($this->sourceList as $target) {
            $visitor = $this->traverser->traverse($target);
            foreach ($visitor->getUses() as $u) {
                $dependensy = new Dependency($u);
                if (!$this->filter($target, $visitor->getFullClassName(), $dependensy)) {
                    continue;
                }
                $this->dependencyList->addDependency($visitor->getFullClassName(), $dependensy);
                $this->sourceList->add($u);
            }
        }
    }
}
