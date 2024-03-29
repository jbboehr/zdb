<?php

namespace zdb\Adapter;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use zdb\Adapter;

abstract class AbstractAdapter implements Adapter, LoggerAwareInterface
{
    /**
     * @var integer
     */
    protected $affectedRows;

    /**
     * @var callable Connection factory function. Will be called on connection timeout to establish a new connection.
     */
    protected $connectionFactory;

    /**
     * @var integer
     */
    protected $insertId;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Holds the total number of queries ran for the database object's lifetime.
     *
     * @var integer
     */
    protected $queryCount = 0;

    /**
     * @var string
     */
    protected $quoteIdentifierChar = '`';

    /**
     * @inheritdoc
     */
    public function getAffectedRows()
    {
        return $this->affectedRows;
    }

    /**
     * @inheritdoc
     */
    public function getInsertId($name = null)
    {
        return $this->insertId;
    }

    /**
     * @inheritdoc
     */
    public function getQueryCount()
    {
        return $this->queryCount;
    }

    /**
     * Set a query logger
     *
     * @param LoggerInterface $logger
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
        return $this;
    }

    /**
     * @param $identifier
     * @return string
     */
    public function quoteIdentifier($identifier)
    {
        $c = $this->quoteIdentifierChar;
        return $c . str_replace(
            '.',
            $c . '.' . $c,
            str_replace($c, $c . $c, $identifier)
        ) . $c;
    }
}
