<?php

namespace ClassGraph;

/**
 * Dependency is ValueObject for keep class name to depends class name list
 */
class Dependency
{
    /** @var string */
    protected $name;
    /** @var string */
    protected $filename;
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
        return $this->name === '' ? $this->getFileName() : $this->name;
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

    /**
     * @return string
     */
    public function getFileName()
    {
        return $this->filename;
    }

    /**
     * @param string $filename
     */
    public function setFileName(string $filename)
    {
        $this->filename = $filename;
    }
}
