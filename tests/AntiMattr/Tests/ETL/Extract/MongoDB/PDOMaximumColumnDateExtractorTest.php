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
        $this->extractor = new PDOMaximumColumnDateExtractorStub(
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

        $this->cursor->expects($this->once())
            ->method('timeout')
            ->will($this->returnValue($this->cursor));

        $batchIterator = $this->extractor->getIterator();
    }

    public function testGetMinimumValue()
    {
        $result = new \stdClass();
        $result->minimum = '2015-08-20 22:03:49';
        $this->extractor->setTimezone('EDT');

        $statement = $this->getMock('PDOStatement');

        $statement->expects($this->once())
            ->method('fetchObject')
            ->will($this->returnValue($result));

        $minDate = $this->extractor->doGetMinimumValue($statement);
        $maxDate = $this->extractor->doGetMaximumValue($statement);

        $this->assertGreaterThan($minDate, $maxDate);
    }
}

class PDOMaximumColumnDateExtractorStub extends PDOMaximumColumnDateExtractor
{
    public function doGetMinimumValue(\PDOStatement $statement)
    {
        return $this->getMinimumValue($statement);
    }

    public function doGetMaximumValue(\PDOStatement $statement)
    {
        return $this->getMaximumValue($statement);
    }
}
