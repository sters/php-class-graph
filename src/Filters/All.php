<?php

namespace ClassGraph\Filters;

class All extends Filter
{
    /** @var []Filter */
    private $filters;

    public function __construct(Filter ...$filters)
    {
        $this->filters = $filters;
    }

    public function do(string $target): bool
    {
        foreach ($this->filters as $filter) {
            if (!$filter->do($target)) {
                return false;
            }
        }

        return true;
    }
}
