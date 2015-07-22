<?php

namespace AntiMattr\Tests\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\MongoDB\PDOMaximumColumnDateExtractor;
use AntiMattr\TestCase\AntiMattrTestCase;

class PDOMaximumColumnDateExtractorTest extends AntiMattrTestCase
{
    private $collection;
    private $collectionName;
    private $column;
    private $connection;
    private $cursor;
    private $db;
    private $extractor;
    private $field;
    private $statement;
    private $table;

    protected function setUp()
    {
        $this->collection = $this->buildMock('MongoCollection');
        $this->collectionName = 'foo';
        $this->column = 'id';
        $this->connection = $this->getMock('AntiMattr\Tests\ETL\MockPDO');
        $this->cursor = $this->buildMock('MongoCursor');
        $this->db = $this->buildMock('MongoDB');
        $this->field = 'bar';
        $this->statement = $this->getMock('AntiMattr\Tests\ETL\MockPDOStatement');
        $this->table = 'my_table';
        $this->extractor = new PDOMaximumColumnDateExtractor(
            $this->db,
            $this->collectionName,
            $this->field,
            $this->connection,
            $this->table,
            $this->column
        );
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Extract\ExtractorInterface', $this->extractor);
    }

    public function testGetIterator()
    {
        $this->db->expects($this->once())
            ->method('__get')
            ->with($this->collectionName)
            ->will($this->returnValue($this->collection));

        $this->connection->expects($this->once())
            ->method('query')
            ->will($this->returnValue($this->statement));

        $this->collection->expects($this->once())
            ->method('find')
            ->will($this->returnValue($this->cursor));

        $this->cursor->expects($this->once())
            ->method('sort')
            ->will($this->returnValue($this->cursor));

        $batchIterator = $this->extractor->getIterator();
    }
}
