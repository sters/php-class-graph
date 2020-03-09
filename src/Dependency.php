<?php

namespace ClassGraph;

/**
 * Dependency is ValueObject for keep class name to depends class name list
 */
class Dependency
{
    /** @var string */
    protected $name;
    /** @var Dependency[] */
    protected $dependency;

    /**
     * Dependency constructor.
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->dependency = [];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Dependency[]
     */
    public function getDependency()
    {
        return $this->dependency;
    }

    /**
     * @return integer
     */
    public function getDependencyCount()
    {
        return count($this->dependency);
    }

    /**
     * @param Dependency $depend
     */
    public function addDependency(Dependency $depend)
    {
        $this->dependency[] = $depend;
    }
}
