<?php

namespace AntiMattr\Tests\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\MongoDB\MongoDBExtractor;
use AntiMattr\TestCase\AntiMattrTestCase;

class MongoDBExtractorTest extends AntiMattrTestCase
{
    private $collection;
    private $collectionName;
    private $cursor;
    private $db;
    private $extractor;

    protected function setUp()
    {
        $this->collection = $this->buildMock('MongoCollection');
        $this->collectionName = 'foo';
        $this->cursor = $this->buildMock('MongoCursor');
        $this->db = $this->buildMock('MongoDB');
        $this->extractor = new MongoDBExtractor($this->db, $this->collectionName);
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
