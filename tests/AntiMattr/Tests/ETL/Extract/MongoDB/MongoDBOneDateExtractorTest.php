<?php

namespace AntiMattr\Tests\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\MongoDB\MongoDBOneDateExtractor;
use AntiMattr\TestCase\AntiMattrTestCase;

class MongoDBOneDateExtractorTest extends AntiMattrTestCase
{
    private $collection;
    private $collectionName;
    private $cursor;
    private $db;
    private $extractor;
    private $field;
    private $modify;

    protected function setUp()
    {
        $this->collection = $this->buildMock('MongoCollection');
        $this->collectionName = 'foo';
        $this->cursor = $this->buildMock('MongoCursor');
        $this->db = $this->buildMock('MongoDB');
        $this->field = 'bar';
        $this->modify = 'yesterday';
        $this->extractor = new MongoDBOneDateExtractor($this->db, $this->collectionName, $this->field, $this->modify);
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
}
