<?php

namespace AntiMattr\Tests\ETL\Transform\Transformer\MongoDB;

use AntiMattr\ETL\Transform\Transformer\MongoDB\MongoIdTransformer;
use AntiMattr\TestCase\AntiMattrTestCase;
use MongoId;

class MongoIdTransformerTest extends AntiMattrTestCase
{
    private $transformation;
    private $transformer;

    protected function setUp()
    {
        $this->transformation = $this->getMock('AntiMattr\ETL\Transform\TransformationInterface');
        $this->transformer = new MongoIdTransformer();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Transform\Transformer\TransformerInterface', $this->transformer);
    }

    public function testNull()
    {
        $this->assertNull($this->transformer->transform(null, $this->transformation));
    }
}
