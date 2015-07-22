<?php

namespace AntiMattr\Tests\ETL\Load\MySQL;

use AntiMattr\ETL\Load\MySQL\MySQLInsertIntoLoader;
use AntiMattr\TestCase\AntiMattrTestCase;

class MySQLInsertIntoLoaderTest extends AntiMattrTestCase
{
    private $connection;
    private $loader;
    private $table;

    protected function setUp()
    {
        $this->connection = $this->getMock('AntiMattr\Tests\ETL\MockPDO');
        $this->table = 'foo';
        $this->loader = new MySQLInsertIntoLoader($this->connection, $this->table);
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Load\LoaderInterface', $this->loader);
    }

    /**
     * @expectedException \AntiMattr\ETL\Exception\LoadException
     */
    public function testLoadEmptyDataThrowsException()
    {
        $this->loader->load();
    }
}
