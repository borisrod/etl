<?php

namespace AntiMattr\Tests\ETL\Load\MySQL;

use AntiMattr\ETL\Load\MySQL\MySQLDeleteByColumnInsertIntoLoader;
use AntiMattr\TestCase\AntiMattrTestCase;

class MySQLDeleteByColumnInsertIntoLoaderTest extends AntiMattrTestCase
{
    private $column;
    private $connection;
    private $loader;
    private $table;

    protected function setUp()
    {
        $this->column = 'bar';
        $this->connection = $this->getMock('AntiMattr\Tests\ETL\MockPDO');
        $this->table = 'foo';
        $this->loader = new MySQLDeleteByColumnInsertIntoLoader($this->connection, $this->table, $this->column);
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
