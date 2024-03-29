<?php

namespace zdb\Test\Adapter;

use zdb\Adapter\MultiplexAdapter;
use zdb\Adapter\NullAdapter;
use zdb\Query\Simple\InsertQuery;
use zdb\Query\Simple\SelectQuery;
use zdb\Test\Common;

class MultiplexAdapterText extends Common
{
    public function testConstruct()
    {
        $reader = $this->getMysqliAdapter();
        $writer = $this->getPdoAdapter();
        $adapter = new MultiplexAdapter($reader, $writer);

        $this->assertTrue($adapter->hasReader());
        $this->assertTrue($adapter->hasWriter());
        $this->assertSame($reader, $adapter->getReader());
        $this->assertSame($writer, $adapter->getWriter());
    }

    public function testSetReader()
    {
        $adapter1 = $this->getMysqliAdapter();
        $adapter2 = $this->getPdoAdapter();
        $adapter = new MultiplexAdapter($adapter1, $adapter2);;

        $orig = $adapter->getReader();
        $adapter->setReader($adapter2);

        $this->assertNotSame($orig, $adapter->getReader());
        $this->assertSame($adapter2, $adapter->getReader());
    }

    public function testSetWriter()
    {
        $adapter1 = $this->getMysqliAdapter();
        $adapter2 = $this->getPdoAdapter();
        $adapter = new MultiplexAdapter($adapter1, $adapter2);

        $orig = $adapter->getWriter();
        $adapter->setWriter($adapter1);

        $this->assertNotSame($orig, $adapter->getWriter());
        $this->assertSame($adapter1, $adapter->getWriter());
    }

    public function testForceWriter()
    {
        $reader = $this->getMock(NullAdapter::class, array('query'));
        $reader->expects($this->never())
            ->method('query');

        $writer = $this->getMock(NullAdapter::class, array('query'));
        $writer->expects($this->once())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->useWriter();
        $adapter->query(new SelectQuery('SELECT TRUE'));
    }

    public function testSelect()
    {
        $reader = $this->getMock(NullAdapter::class, array('query'));
        $reader->expects($this->once())
            ->method('query');

        $writer = $this->getMock(NullAdapter::class, array('query'));
        $writer->expects($this->never())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->query(new SelectQuery('SELECT TRUE'));
    }

    public function testInsert()
    {
        $reader = $this->getMock(NullAdapter::class, array('query'));
        $reader->expects($this->never())
            ->method('query');

        $writer = $this->getMock(NullAdapter::class, array('query'));
        $writer->expects($this->once())
            ->method('query');

        $adapter = new MultiplexAdapter($reader, $writer);

        $adapter->useWriter();
        $adapter->query(new InsertQuery('does not need to be valid'));
    }
}
