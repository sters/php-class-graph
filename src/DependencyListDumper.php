<?php

namespace ClassGraph;

/**
 * DependencyListDumper can output to text from DependencyList
 */
class DependencyListDumper
{
    /** @var DependencyList */
    protected $list;

    protected function getHeader(): string
    {
        return "";
    }
    protected function getFooter(): string
    {
        return "";
    }

    /**
     * Format class name for Dependency->getName()
     * Use at class list definition
     *
     * @param Dependency $d current class
     * @param array $counter unique number aliases for class names
     * @return string formatted text
     */
    protected function formatClass(Dependency $d, array $counter): string
    {
        return '';
    }

    /**
     * Format graph for 1 current class to 1 depends class
     * Use at make class list graph
     *
     * @param Dependency $d current class
     * @param Dependency $child depends class
     * @param array $counter unique number aliases for class names
     * @param int $depSize depth
     * @return string formatted class graph
     */
    protected function formatGraph(Dependency $d, Dependency $child, array $counter, int $depSize): string
    {
        return sprintf(
            "%s -> %s",
            $d->getName(),
            $child->getName()
        );
    }

    public function __construct(DependencyList $list)
    {
        $this->list = $list;
    }

    /**
     * Dump to text from DependencyList
     *
     * @return string dumpped text
     */
    public function dump(): string
    {
        $counter = [];
        $output = [$this->getHeader()];

        foreach ($this->list->getRelations() as $_ => $d) {
            if (empty($counter[$d->getName()])) {
                $counter[$d->getName()] = count($counter);
                $output[] = $this->formatClass($d, $counter);
            }

            $depSize = count($d->getDependency());

            foreach ($d->getDependency() as $child) {
                if (empty($counter[$child->getName()])) {
                    $counter[$child->getName()] = count($counter);
                    $output[] = $this->formatClass($child, $counter);
                }

                $output[] = $this->formatGraph($d, $child, $counter, $depSize);
            }
        }

        $output[] = $this->getFooter();

        return implode("\n", array_filter($output, function ($o) {
            return !empty($o);
        })) . "\n";
    }
}
