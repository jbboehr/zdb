<?php

namespace zdb\Test\Adapter;

use zdb\Test\Common;
use zdb\Test\Fixture;

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
     * @dataProvider adapterProvider
     */
    public function testSelect(Adapter $adapter)
    {
        $select = new SelectQuery('SELECT * FROM fixture1 ORDER BY id ASC');
        $result = $adapter->query($select);
        $objects = $result->fetchAll();

        // Asserts
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(2, $objects);
        $this->assertInstanceOf(\stdClass::class, $objects[0]);
        $this->assertInstanceOf(\stdClass::class, $objects[1]);
        $this->assertSame('1', $objects[0]->id);
        $this->assertSame('2', $objects[1]->id);
        $this->assertSame(1, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testSelectWithString(Adapter $adapter)
    {
        $select = 'SELECT * FROM fixture1 ORDER BY id ASC';
        $result = $adapter->query($select);
        $objects = $result->fetchAll();

        // Asserts
        $this->assertInstanceOf(Result::class, $result);
        $this->assertCount(2, $objects);
        $this->assertInstanceOf(\stdClass::class, $objects[0]);
        $this->assertInstanceOf(\stdClass::class, $objects[1]);
        $this->assertSame('1', $objects[0]->id);
        $this->assertSame('2', $objects[1]->id);
        $this->assertSame(1, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testInsert(Adapter $adapter)
    {
        $id = mt_rand(0, PHP_INT_MAX);
        $insert = new InsertQuery('INSERT INTO fixture3 VALUES(' . $id . ')');
        $result = $adapter->query($insert);

        // Asserts
        $this->assertEquals($id, $result);
        $this->assertEquals($id, $adapter->getInsertId());
        $this->assertSame(1, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testInsertWithString(Adapter $adapter)
    {
        $id = mt_rand(0, PHP_INT_MAX);
        $insert = 'INSERT INTO fixture3 VALUES(' . $id . ')';
        $adapter->query($insert);

        // Asserts
        $this->assertSame(1, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testUpdate(Adapter $adapter)
    {
        $val = mt_rand(0, PHP_INT_MAX);
        $update = new UpdateQuery('UPDATE fixture2 SET dval = ' . $val . ' WHERE id = 1');
        $result = $adapter->query($update);

        // Asserts
        $this->assertSame(1, $result);
        $this->assertSame(1, $adapter->getAffectedRows());
        $this->assertSame(1, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testDelete(Adapter $adapter)
    {
        // Make the row to delete ...
        $id = mt_rand(0, PHP_INT_MAX);
        $insert = new InsertQuery('INSERT INTO fixture3 VALUES(' . $id . ')');
        $adapter->query($insert);

        // Delete
        $delete = new DeleteQuery('DELETE FROM fixture3 WHERE id = ' . $id . '');
        $result = $adapter->query($delete);

        // Asserts
        $this->assertSame(1, $result);
        $this->assertSame(1, $adapter->getAffectedRows());
        $this->assertSame(2, $adapter->getQueryCount());
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testInvalidQuery(Adapter $adapter)
    {
        $this->setExpectedException(Exception\RuntimeException::class);
        $select = new SelectQuery('SELECT rwotwer NOT');
        $adapter->query($select);
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testSetLogger(Adapter $adapter)
    {
        $logger = $this->getMock(\Psr\Log\NullLogger::class, array('debug'));
        $logger->expects($this->once())
            ->method('debug');
        $adapter->setLogger($logger);
        $adapter->query(new SelectQuery('SELECT TRUE'));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testSetLoggerWithStringQuery(Adapter $adapter)
    {
        $logger = $this->getMock(\Psr\Log\NullLogger::class, array('debug'));
        $logger->expects($this->once())
            ->method('debug');
        $adapter->setLogger($logger);
        $adapter->query('SELECT TRUE');
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testSetLoggerWithInvalidQuery(Adapter $adapter)
    {
        $this->setExpectedException(Exception\RuntimeException::class);
        $logger = $this->getMock(\Psr\Log\NullLogger::class, array('debug', 'error'));
        $logger->expects($this->once())
            ->method('debug');
        $logger->expects($this->once())
            ->method('error');
        $adapter->setLogger($logger);
        $adapter->query('SELECT WETRW#$Twe4gtwefr');
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testQuote(Adapter $adapter)
    {
        $this->assertEquals('NULL', $adapter->quote(null));
        $this->assertEquals("'1'", $adapter->quote(true));
        $this->assertEquals("''", $adapter->quote(false));
        $this->assertEquals('"', $adapter->quote(new Expression('"')));
        $this->assertEquals("'100'", $adapter->quote(100));
        $this->assertEquals("'3.14'", $adapter->quote(3.14));
        $this->assertEquals("'blah'", $adapter->quote('blah'));
    }

    /**
     * @param Adapter $adapter
     * @dataProvider adapterProvider
     */
    public function testQuoteIdentifier(Adapter $adapter)
    {
        $this->assertEquals('`table`', $adapter->quoteIdentifier('table'));
        $this->assertEquals('```table```', $adapter->quoteIdentifier('`table`'));
        $this->assertEquals('`database`.`table`', $adapter->quoteIdentifier('database.table'));
        $this->assertEquals('```database```.```table```', $adapter->quoteIdentifier('`database`.`table`'));
    }
}

