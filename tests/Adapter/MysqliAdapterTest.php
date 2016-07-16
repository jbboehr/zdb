<?php

namespace zdb\Test\Adapter;

use zdb\Adapter\MysqliAdapter;
use zdb\Test\Common;

class MysqliAdapterText extends Common
{
    public function testSetConnection()
    {
        $mysqli1 = $this->getMysqli();
        $mysqli2 = $this->getMysqli();
        $adapter = new MysqliAdapter($mysqli1);

        $this->assertSame($mysqli1, $adapter->getConnection());
        $adapter->setConnection($mysqli2);
        $this->assertSame($mysqli2, $adapter->getConnection());
        $this->assertNotSame($mysqli1, $adapter->getConnection());

    }
}
