<?php

namespace ClassGraph;

/**
 * DependencyList has Dependency(ClassName) - Dependencies relations
 */
class DependencyList
{
    /** @var Dependency[] */
    protected $relations = [];

    /**
     * Register new dependency class
     *
     * @param Dependency $dependency from depends
     * @return void
     */
    public function register(Dependency $dependency)
    {
        if (!empty($this->relations[$dependency->getName()])) {
            return;
        }
        $this->relations[$dependency->getName()] = $dependency;
    }

    /**
     * Add dependency to $baseClassName
     *
     * @param string $baseClassName from depends
     * @param Dependency $targetClass depends to
     * @return void
     */
    public function addDependency(string $baseClassName, Dependency $targetClass)
    {
        if (empty($this->relations[$baseClassName])) {
            $this->register(new Dependency($baseClassName));
        }
        $this->relations[$baseClassName]->addDependency($targetClass);
    }

    /**
     * @return Dependency[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
