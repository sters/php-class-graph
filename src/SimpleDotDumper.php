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
        return implode([
            "digraph \"classes-dependency\" {",
            "\tnormalize=true",
        ], "\n");
    }
    protected function getFooter(): string
    {
        return "}";
    }

    protected function formatClass(Dependency $d, array $counter): string
    {
        return "";
    }

    protected function formatGraph(Dependency $d, Dependency $child, array $counter, int $depsize): string
    {
        return sprintf(
            "\t\"%s\" -> \"%s\" [ minlen = %d, len = %d ];",
            str_replace('\\', '\\\\', $d->getName()),
            str_replace('\\', '\\\\', $child->getName()),
            $depsize,
            $depsize * 2
        );
    }
}
