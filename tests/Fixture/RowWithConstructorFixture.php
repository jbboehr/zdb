<?php

namespace zdb\Test\Fixture;

class RowWithConstructorFixture
{
    public $argument;

    public function __construct($argument)
    {
        $this->argument = $argument;
    }
}
