<?php

namespace zdb;

interface Adapter
{
    /**
     * Get affected rows
     * @return integer
     */
    public function getAffectedRows();

    /**
     * Get the last insert ID
     * @param string $name
     * @return integer
     */
    public function getInsertId($name = null);

    /**
     * Gets number of queries run using this adapter.
     *
     * @return integer
     */
    public function getQueryCount();

    /**
     * Executes an SQL query
     *
     * @param Query|string $query
     * @return Result|integer|boolean
     * @throws Exception
     */
    public function query($query);

    /**
     * Quote a value
     *
     * @param string|Expression $string
     * @return string
     */
    public function quote($string);

    /**
     * Quote an identifier
     *
     * @param $identifier
     * @return string
     */
    public function quoteIdentifier($identifier);
}
