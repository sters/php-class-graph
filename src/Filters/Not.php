<?php

namespace ClassGraph\Filters;

class Not extends Filter
{
    private $filter;

    public function __construct(Filter $filter)
    {
        $this->filter = $filter;
    }

    public function do(string $target): bool
    {
        return !$this->filter($target);
    }
}
