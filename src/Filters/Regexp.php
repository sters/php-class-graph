<?php

namespace ClassGraph\Filters;

class Regexp extends Filter
{
    /** @var string */
    protected $filter;

    public function __construct(string $msg)
    {
        $this->filter = '/' . preg_quote($msg, '/') . '/';
    }

    public function do(string $target): bool
    {
        return preg_match($this->filter, $target);
    }
}
