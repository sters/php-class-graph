<?php

namespace ClassGraph;

class Dumper
{
    /** @var DependencyList */
    protected $list;

    protected function getHeader(): string { return ""; }
    protected function getFooter(): string { return ""; }

    protected function formatClass(Dependency $d, array $counter): string
    {
        return sprintf(
            "\"%s\" as c%04d",
            $d->getName(),
            $counter[$d->getName()]
        );
    }

    protected function formatGraph(Dependency $d, Dependency $child, array $counter): string
    {
        return sprintf(
            "c%04d -> c%04d",
            $counter[$d->getName()],
            $counter[$child->getName()]
        );
    }

    public function __construct(DependencyList $list)
    {
        $this->list = $list;
    }

    public function dump(): string
    {
        $counter = [];
        $output = [$this->getHeader()];

        foreach ($this->list->getRelations() as $_ => $d) {
            if (empty($counter[$d->getName()])) {
                $counter[$d->getName()] = count($counter);
                $output[] = $this->formatClass($d, $counter);
            }

            foreach ($d->getDependency() as $child) {
                if (empty($counter[$child->getName()])) {
                    $counter[$child->getName()] = count($counter);
                    $output[] = $this->formatClass($child, $counter);
                }

                $output[] = $this->formatGraph($d, $child, $counter);
            }
        }

        $output[] = $this->getFooter();

        return implode("\n", array_filter($output, function($o) {
            return !empty($o);
        })) . "\n";
    }
}
