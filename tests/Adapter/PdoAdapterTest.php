<?php

namespace zdb\Test\Adapter;

use zdb\Adapter\MysqliAdapter;
use zdb\Adapter\PdoAdapter;
use zdb\Test\Common;

class PdoAdapterText extends Common
{
    public function testSetConnection()
    {
        $pdo1 = $this->getPdo();
        $pdo2 = $this->getPdo();
        $adapter = new PdoAdapter($pdo1);

        $this->assertSame($pdo1, $adapter->getConnection());
        $adapter->setConnection($pdo2);
        $this->assertSame($pdo2, $adapter->getConnection());
        $this->assertNotSame($pdo1, $adapter->getConnection());

    }
}
