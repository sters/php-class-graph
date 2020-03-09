<?php

namespace ClassGraph;

/**
 * SimpleDotDumper is wrapper of DependencyListDumper.
 * This class provides dot data from DependencyList.
 */
class SimpleDotDumper extends DependencyListDumper
{
    protected function getHeader(): string
    {
        return "digraph \"classes-dependency\" {";
    }
    protected function getFooter(): string
    {
        return "}";
    }

    protected function formatClass(Dependency $d, array $counter): string
    {
        return "";
    }

    protected function formatGraph(Dependency $d, Dependency $child, array $counter): string
    {
        return sprintf(
            "\t\"%s\" -> \"%s\" [ minlen = 4 ];",
            str_replace('\\', '\\\\', $d->getName()),
            str_replace('\\', '\\\\', $child->getName())
        );
    }
}
