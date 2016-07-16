<?php

namespace zdb\Adapter;

use PDO;
use PDOStatement;
use zdb\Exception;
use zdb\Expression;
use zdb\Query;
use zdb\Result\PdoResult as Result;

class PdoAdapter extends AbstractAdapter
{
    /**
     * @var PDO
     */
    protected $connection;

    /**
     * Construct a new database object.
     *
     * @param PDO $connection
     */
    public function __construct(PDO $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Exposes the local connection object
     *
     * @return PDO
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * Sets the local mysqli object
     *
     * @param PDO $connection
     * @return $this
     */
    public function setConnection(PDO $connection = null)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function query($query)
    {
        $connection = $this->getConnection();

        $this->affectedRows = null;
        $this->insertId = null;
        $this->queryCount++;

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
        $ret = $connection->query($queryString);

        // Save insert ID if instance of insert
        if( $query instanceof Query\InsertQuery ) {
            $this->insertId = $connection->lastInsertId();
        }

        // Save affected rows
        if( $ret instanceof PDOStatement ) {
            $this->affectedRows = $ret->rowCount();
        }

        // Handle result
        if( $ret !== false ) {
            if( $query instanceof Query\InsertQuery ) {
                return $this->getInsertId();
            } else if( $query instanceof Query\UpdateQuery || $query instanceof Query\DeleteQuery ) {
                return $this->getAffectedRows();
            } else if( $ret instanceof PDOStatement ) {
                return new Result($ret);
            }
            return $ret;
        } else {
            $err = $connection->errorInfo();
            $message = sprintf(
                "%s: %s\n%s",
                $connection->errorCode(),
                $err[2],
                $queryString
            );
            // Log error
            if( $this->logger ) {
                $this->logger->error($message);
            }
            // Query failed, throw exception
            throw new Exception\RuntimeException($message, (integer) $connection->errorCode());
        }
    }

    public function quote($value)
    {
        if( $value instanceof Expression ) {
            return $value->toString();
        } else if ( null === $value ) {
            return 'NULL';
        }

        $type = PDO::PARAM_STR;
        switch( gettype($value) ) {
            case 'boolean': $type = PDO::PARAM_BOOL; break;
            case 'integer': $type = PDO::PARAM_INT; break;
            case 'float': $type = PDO::PARAM_STR; break;
            case 'object': settype($value, 'string'); break;
        }
        return $this->connection->quote($value, $type);
    }
}
