<?php

namespace zdb\Test\Result;

use zdb\Test\Common;
use zdb\Test\Fixture;

use mysqli;
use PDO;
use zdb\Adapter;
use zdb\Exception;
use zdb\Expression;
use zdb\Query\Simple\DeleteQuery;
use zdb\Query\Simple\InsertQuery;
use zdb\Query\Simple\SelectQuery;
use zdb\Query\Simple\UpdateQuery;
use zdb\Result;

class IntegrationTest extends Common
{
    /**
     * @param Adapter $adapter
     * @return Result
     */
    protected function getQueryResult(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture2 ORDER BY id ASC');
        return $adapter->query($select);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchAllAssoc(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_ASSOC);
        $rows = $result->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertInternalType('array', $rows[0]);
        $this->assertInternalType('string', key($rows[0]));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchAllColumn(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_COLUMN);
        $rows = $result->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertInternalType('string', $rows[0]); // this could be an integer someday
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchAllNum(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_NUM);
        $rows = $result->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertInternalType('array', $rows[0]);
        $this->assertInternalType('integer', key($rows[0]));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchAllObject(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_OBJECT);
        $result->setResultClass(null);
        $rows = $result->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertInternalType('object', $rows[0]);
        $this->assertInstanceOf(\stdClass::class, $rows[0]);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchAllObjectWithParams(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultClass(Fixture\RowWithConstructorFixture::class);
        $result->setResultParams(array('value'));
        $result->setResultMode(Result::FETCH_OBJECT);
        $rows = $result->fetchAll();

        $this->assertInternalType('array', $rows);
        $this->assertInternalType('object', $rows[0]);
        $this->assertInstanceOf(Fixture\RowWithConstructorFixture::class, $rows[0]);
        $this->assertSame('value', $rows[0]->argument);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchRowAssoc(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_ASSOC);
        $row = $result->fetchRow();

        $this->assertInternalType('array', $row);
        $this->assertInternalType('string', key($row));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchRowColumn(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_COLUMN);
        $col = $result->fetchRow();

        $this->assertInternalType('string', $col);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchRowColumnNoResults(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture2 WHERE FALSE');
        $result = $adapter->query($select);
        $result->setResultMode(Result::FETCH_COLUMN);
        $col = $result->fetchRow();

        $this->assertNull($col);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchRowNum(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_NUM);
        $row = $result->fetchRow();

        $this->assertInternalType('array', $row);
        $this->assertInternalType('integer', key($row));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchRowObject(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $result->setResultMode(Result::FETCH_OBJECT);
        $result->setResultClass(null);
        $row = $result->fetchRow();

        $this->assertInternalType('object', $row);
        $this->assertInstanceOf(\stdClass::class, $row);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testFetchColumn(Adapter $adapter)
    {
        $result = $this->getQueryResult($adapter);
        $col = $result->fetchColumn();

        $this->assertInternalType('string', $col);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testResultClass(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC LIMIT 1');
        $result = $adapter->query($select);
        $result->setResultClass(Fixture\RowFixture::class);
        $row = $result->fetchRow();

        $this->assertSame(Fixture\RowFixture::class, $result->getResultClass());
        $this->assertInstanceOf(Fixture\RowFixture::class, $row);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testResultClassWithParams(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC LIMIT 1');
        $result = $adapter->query($select);
        $result->setResultClass(Fixture\RowWithConstructorFixture::class);
        $result->setResultParams(array('value'));
        $row = $result->fetchRow();

        $this->assertEquals(Fixture\RowWithConstructorFixture::class, $result->getResultClass());
        $this->assertEquals(array('value'), $result->getResultParams());
        $this->assertInstanceOf(Fixture\RowWithConstructorFixture::class, $row);
        $this->assertSame('value', $row->argument);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testSetResultMode(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC LIMIT 1');
        $result = $adapter->query($select);
        $result->setResultMode(Result::FETCH_ASSOC);
        $this->assertTrue(is_array($result->fetchRow()));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testInvalidResultClass(Adapter $adapter)
    {
        $this->setExpectedException(Exception\ClassNotFoundException::class);
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC LIMIT 1');
        $result = $adapter->query($select);
        $result->setResultClass('ClassThatDoesNotExist');
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testInvalidResultMode(Adapter $adapter)
    {
        $this->setExpectedException(Exception\InvalidArgumentException::class);
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC LIMIT 1');
        $result = $adapter->query($select);
        $result->setResultMode(1488);
    }
}
