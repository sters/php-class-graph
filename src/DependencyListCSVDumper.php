<?php

namespace ClassGraph;

/**
 * DependencyListCSVDumper is wrapper of DependencyListDumper.
 * This class provides csv data from DependencyList.
 */
class DependencyListCSVDumper extends DependencyListDumper
{
    protected function getHeader(): string
    {
        return 'Base Class,Depend Class';
    }

    protected function formatClass(Dependency $d, array $counter): string
    {
        return '';
    }

    protected function formatGraph(Dependency $d, Dependency $child, array $counter): string
    {
        return sprintf(
            "%s,%s",
            $d->getName(),
            $child->getName()
        );
    }
}
