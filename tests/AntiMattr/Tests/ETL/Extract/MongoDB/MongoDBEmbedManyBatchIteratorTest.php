<?php

namespace AntiMattr\Tests\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\MongoDB\MongoDBEmbedManyBatchIterator;
use AntiMattr\TestCase\AntiMattrTestCase;
use MongoId;

class MongoDBEmbedManyBatchIteratorTest extends AntiMattrTestCase
{
    private $batchIterator;
    private $innerIterator;

    protected function setUp()
    {
        $this->batchIterator = new MongoDBEmbedManyBatchIterator('flashes');
        $this->innerIterator = $this->createInnerIterator();
        $this->batchIterator->setInnerIterator($this->innerIterator);
    }

    public function testEmbedManyIterator()
    {
        $data = [];
        $expectedKey = 0;
        foreach ($this->batchIterator as $key => $iteration) {
            $this->assertEquals($expectedKey, $key);
            $data[] = $iteration;
            $expectedKey++;
        }

        $this->assertEquals(8, count($data));

        $this->assertEquals('559a9b8b4c3d6f33768b5482', (string) $data[0]['_id']);
        $this->assertEquals('559a9b8b4c3d6f33768b5482', (string) $data[1]['_id']);
        $this->assertEquals('559a9b8b4c3d6f33768b5482', (string) $data[2]['_id']);

        $this->assertEquals('559a59674a3d6f937e8b4bdc', (string) $data[3]['_id']);
        $this->assertEquals('559a59674a3d6f937e8b4bdc', (string) $data[4]['_id']);

        $this->assertEquals('559a1b094e3d6f6a568b8ae6', (string) $data[5]['_id']);
        $this->assertEquals('559a1b094e3d6f6a568b8ae6', (string) $data[6]['_id']);
        $this->assertEquals('559a1b094e3d6f6a568b8ae6', (string) $data[7]['_id']);

        $this->assertEquals('559a9d13483d6fdb638ba477', (string) $data[0]['flashes']['_id']);
        $this->assertEquals('559a9b27683d6fd8078b4668', (string) $data[1]['flashes']['_id']);
        $this->assertEquals('559a99a84f3d6f8c098b70ec', (string) $data[2]['flashes']['_id']);

        $this->assertEquals('559a5a49493d6ffa248ba108', (string) $data[3]['flashes']['_id']);
        $this->assertEquals('559a55ac483d6fff638ba0ba', (string) $data[4]['flashes']['_id']);

        $this->assertEquals('559a1e354d3d6f1e408b9fc5', (string) $data[5]['flashes']['_id']);
        $this->assertEquals('5599f5874e3d6f77568b8af7', (string) $data[6]['flashes']['_id']);
        $this->assertEquals('5599edeb4e3d6f64568b8982', (string) $data[7]['flashes']['_id']);
    }

    private function createInnerIterator()
    {
        $array = [
            [
                '_id' => new MongoId('559a9b8b4c3d6f33768b5482'),
                'flashes' => [
                    [
                        '_id' => new MongoId('559a9d13483d6fdb638ba477'),
                    ],
                    [
                        '_id' => new MongoId('559a9b27683d6fd8078b4668'),
                    ],
                    [
                        '_id' => new MongoId('559a99a84f3d6f8c098b70ec'),
                    ],
                ],
            ],
            [
                '_id' => new MongoId('559a59674a3d6f937e8b4bdc'),
                'flashes' => [
                    [
                        '_id' => new MongoId('559a5a49493d6ffa248ba108'),
                    ],
                    [
                        '_id' => new MongoId('559a55ac483d6fff638ba0ba'),
                    ],
                ],
            ],
            [
                '_id' => new MongoId('559a1b094e3d6f6a568b8ae6'),
                'flashes' => [
                    [
                        '_id' => new MongoId('559a1e354d3d6f1e408b9fc5'),
                    ],
                    [
                        '_id' => new MongoId('5599f5874e3d6f77568b8af7'),
                    ],
                    [
                        '_id' => new MongoId('5599edeb4e3d6f64568b8982'),
                    ],
                ],
            ],
        ];

        return new \ArrayIterator($array);
    }
}