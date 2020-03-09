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
    public function addDependency(Dependency $baseClass, Dependency $targetClass)
    {
        if (empty($this->relations[$baseClass->getName()])) {
            $this->register($baseClass);
        }
        $this->relations[$baseClass->getName()]->addDependency($targetClass);
    }

    /**
     * @return Dependency[]
     */
    public function getRelations(): array
    {
        return $this->relations;
    }
}
