<?php

namespace zdb\Adapter;

use mysqli;
use mysqli_result;
use zdb\Exception;
use zdb\Expression;
use zdb\Result\MysqliResult as Result;
use zdb\Query;

class MysqliAdapter extends AbstractAdapter
{
    /**
     * @var mysqli
     */
    protected $connection;

    /**
     * @param mysqli $connection
     */
    public function __construct(mysqli $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return mysqli
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param mysqli $connection
     * @return $this
     */
    public function setConnection(mysqli $connection = null)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * Executes an SQL query.
     *
     * When given a QueryBuilder instance as an argument, the return value is based on the class:
     * Select queries produce an instance of Select
     * Insert returns the insert ID
     * Update and Delete return the affected rows
     * string queries will return the value returned by the internal adapter
     *
     * @param string|Query $query
     * @return Result|integer|boolean
     * @throws Exception
     */
    public function query($query)
    {
        $connection = $this->getConnection();

        $this->affectedRows = null;
        $this->insertId = null;
        $this->queryCount++;

        // __toString is not allowed to throw exceptions
        if( $query instanceof Query ) {
            $queryString = $query->toString();
        } else {
            $queryString = (string) $query;
        }

        // Log query
        if( $this->logger ) {
            $this->logger->debug($queryString);
        }

        // Execute query
        $ret = $connection->query($queryString, MYSQLI_STORE_RESULT);

        // Save insert ID if instance of insert
        if( $query instanceof Query\InsertQuery ) {
            $this->insertId = $connection->insert_id;
        }

        // Save affected rows
        $this->affectedRows = $connection->affected_rows;

        // Handle result
        if( $ret !== false ) {
            if( $ret instanceof mysqli_result ) {
                return new Result($ret);
            } else if( $query instanceof Query\InsertQuery ) {
                return $this->getInsertId();
            } else if( $query instanceof Query\UpdateQuery || $query instanceof Query\DeleteQuery ) {
                return $this->getAffectedRows();
            }
            return $ret;
        } else {
            $message = sprintf(
                "%s: %s\n%s",
                $connection->errno,
                $connection->error,
                $queryString
            );
            // Log error
            if( $this->logger ) {
                $this->logger->error($message);
            }
            // Query failed, throw exception
            throw new Exception\RuntimeException($message, $connection->errno);
        }
    }

    /**
     * Quote a raw string
     *
     * @param string $value
     * @return string
     */
    public function quote($value)
    {
        if( null === $value ) {
            return 'NULL';
        } else if( is_bool($value) ) {
            return ( $value ? "'1'" : "''" );
        } else if( $value instanceof Expression ) {
            return $value->toString();
        } else if( is_integer($value) ) {
            return sprintf("'%d'", $value);
        } else if( is_float($value) ) {
            return sprintf("'%g'", $value);
        } else {
            return "'" . $this->connection->real_escape_string($value) . "'";
        }
    }
}
