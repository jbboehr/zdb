<?php

namespace zdb\Query;

abstract class Simple
{
    /**
     * @var string
     */
    private $query;

    /**
     * Simple constructor.
     * @param string $query
     */
    public function __construct($query)
    {
        $this->query = (string) $query;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return $this->query;
    }
}
