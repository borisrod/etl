<?php

namespace AntiMattr\Tests\ETL\Transform\Transformer\MongoDB;

use AntiMattr\ETL\Transform\Transformer\MongoDB\MongoDateTransformer;
use AntiMattr\TestCase\AntiMattrTestCase;
use MongoId;

class MongoDateTransformerTest extends AntiMattrTestCase
{
    private $transformation;
    private $transformer;

    protected function setUp()
    {
        $this->transformation = $this->getMock('AntiMattr\ETL\Transform\TransformationInterface');
        $this->transformer = new MongoDateTransformer();
        $this->transformer->options['format'] = 'Y-m-d H:i:s';
        $this->transformer->options['timezone'] = 'America/New_York';
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Transform\Transformer\TransformerInterface', $this->transformer);
    }

    public function testNull()
    {
        $this->assertNull($this->transformer->transform(null, $this->transformation));
    }

    public function testTransformNotDate()
    {
        $this->assertNotNull($this->transformer->transform('foo', $this->transformation));
    }

    public function testTransform()
    {
        $timetamp = '1441129401'; # '2015-09-01 17:43:21 GMT' , '2015-09-01 12:43:21' CST, '2015-09-01 13:43:21' EST,
        $mongoDate = new \MongoDate($timetamp);
        $transformed = $this->transformer->transform($mongoDate, $this->transformation);

        $this->assertEquals('2015-09-01 13:43:21', $transformed);
    }
}
