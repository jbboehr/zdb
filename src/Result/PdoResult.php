<?php

namespace zdb\Result;

use PDO;
use PDOStatement;
use zdb\Exception;

class PdoResult extends AbstractResult
{
    /**
     * @var PDOStatement
     */
    protected $result;

    /**
     * Constructor
     *
     * @param PDOStatement $result
     */
    public function __construct(PDOStatement $result)
    {
        $this->result = $result;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->free();
    }

    /**
     * Frees the local result object, and unsets it.
     *
     * @return void
     */
    public function free()
    {
        if( $this->result ) {
            //$this->result->free();
            $this->result = null;
        }
    }

    /**
     * Getter function for the local result object.
     *
     * @return PDOStatement
     * @throws Exception\RuntimeException
     */
    public function getResult()
    {
        return $this->result;
    }

    public function fetchRow($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = null;
        switch( $mode ) {
            case self::FETCH_ASSOC:
                $data = $spec->fetch(PDO::FETCH_ASSOC);
                break;
            case self::FETCH_COLUMN:
                $data = $spec->fetch(PDO::FETCH_COLUMN) ?: null;
                break;
            case self::FETCH_NUM:
                $data = $spec->fetch(PDO::FETCH_NUM);
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        $data = $spec->fetchObject($this->resultClass, $this->resultParams);
                    } else {
                        $data = $spec->fetchObject($this->resultClass);
                    }
                } else {
                    $data = $spec->fetchObject();
                }
                break;
        }

        //$this->free();
        return $data;
    }


    public function fetchAll($mode = null)
    {
        $spec = $this->getResult();

        if( $mode === null ) {
            $mode = $this->getResultMode();
        }

        $data = array();
        switch( $mode ) {
            case self::FETCH_ASSOC:
                $data = $spec->fetchAll(PDO::FETCH_ASSOC);
                break;
            case self::FETCH_COLUMN:
                $data = $spec->fetchAll(PDO::FETCH_COLUMN);
                break;
            case self::FETCH_NUM:
                $data = $spec->fetchAll(PDO::FETCH_NUM);
                break;
            case self::FETCH_OBJECT:
                if( null !== $this->resultClass ) {
                    if( null !== $this->resultParams ) {
                        while( ($row = $spec->fetchObject($this->resultClass, $this->resultParams)) ) {
                            $data[] = $row;
                        }
                    } else {
                        while( ($row = $spec->fetchObject($this->resultClass)) ) {
                            $data[] = $row;
                        }
                    }
                } else {
                    while( ($row = $spec->fetchObject()) ) {
                        $data[] = $row;
                    }
                }
                break;
        }

        //$this->free();
        return $data;
    }

    public function fetchColumn()
    {
        return $this->result->fetchColumn();
    }
}
