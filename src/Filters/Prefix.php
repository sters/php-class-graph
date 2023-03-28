<?php

namespace ClassGraph\Filters;

class Prefix extends Regexp
{
    public function __construct(string $msg)
    {
        $this->filter = '/^' . preg_quote($msg, '/') . '/';
    }
}
