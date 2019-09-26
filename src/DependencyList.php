<?php
namespace ClassGraph;

class DependencyList
{
    /** @var Dependency[] */
    protected $relations = [];

    public function register(Dependency $dependency)
    {
        if (!empty($this->relations[$dependency->getName()])) {
            return;
        }
        $this->relations[$dependency->getName()] = $dependency;
    }

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
