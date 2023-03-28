<?php

namespace ClassGraph\Filters;

abstract class Filter
{
    abstract public function do(string $target): bool;
}
