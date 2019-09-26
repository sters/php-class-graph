<?php
namespace ClassGraph;

class UmlDumper extends Dumper
{
    protected function formatClass(Dependency $d, array $counter): string
    {
        return sprintf(
            "card \"%s\" as c%04d",
            $d->getName(),
            $counter[$d->getName()]
        );
    }

    protected function formatGraph(Dependency $d, Dependency $child, array $counter): string
    {
        return sprintf(
            "c%04d ---> c%04d",
            $counter[$d->getName()],
            $counter[$child->getName()]
        );
    }
}
