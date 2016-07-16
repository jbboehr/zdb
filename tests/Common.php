<?php

namespace zdb\Test;

use mysqli;
use PDO;
use ReflectionClass;
use PHPUnit_Framework_TestCase;
use zdb\Adapter;

class Common extends PHPUnit_Framework_TestCase
{
    public function getReflectedPropertyValue($class, $propertyName)
    {
        $reflectedClass = new ReflectionClass($class);
        $property = $reflectedClass->getProperty($propertyName);
        $property->setAccessible(true);

        return $property->getValue($class);
    }

    /**
     * @return PDO
     */
    protected function getPdo()
    {
        $pdo = new PDO(
            sprintf('mysql:host=%s;dbname=%s;', ZDB_TEST_DATABASE_HOST, ZDB_TEST_DATABASE_DBNAME),
            ZDB_TEST_DATABASE_USERNAME,
            ZDB_TEST_DATABASE_PASSWORD
        );
        return $pdo;
    }

    protected function getPdoAdapter()
    {
        return new Adapter\PdoAdapter($this->getPdo());
    }

    protected function getMysqli()
    {
        $mysqli = new mysqli();
        $mysqli->connect(
            ZDB_TEST_DATABASE_HOST,
            ZDB_TEST_DATABASE_USERNAME,
            ZDB_TEST_DATABASE_PASSWORD,
            ZDB_TEST_DATABASE_DBNAME
        );
        return $mysqli;
    }

    protected function getMysqliAdapter()
    {
        return new Adapter\MysqliAdapter($this->getMysqli());
    }

    protected function getMultiplexAdapter()
    {
        return new Adapter\MultiplexAdapter($this->getMysqliAdapter(), $this->getPdoAdapter());
    }

    /**
     * @return Adapter[][]
     */
    public function adapterProvider()
    {
        return array(
            array($this->getMysqliAdapter()),
            array($this->getPdoAdapter()),
            array($this->getMultiplexAdapter()),
        );
    }
}
