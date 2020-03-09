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
            $base = new Dependency($visitor->getFullClassName());
            $base->setFileName($target);
            foreach ($visitor->getUses() as $u) {
                $dependency = new Dependency($u);
                if (!call_user_func($this->filter, $target, $visitor->getFullClassName(), $dependency)) {
                    continue;
                }
                $this->dependencyList->addDependency($base, $dependency);
                $this->sourceList->add($u);
            }
        }
    }
}
