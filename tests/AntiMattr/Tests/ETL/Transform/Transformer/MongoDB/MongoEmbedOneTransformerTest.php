<?php

namespace AntiMattr\Tests\ETL\Transform\Transformer\MongoDB;

use AntiMattr\ETL\Transform\Transformer\MongoDB\MongoEmbedOneTransformer;
use AntiMattr\TestCase\AntiMattrTestCase;
use MongoId;

class MongoEmbedOneTransformerTest extends AntiMattrTestCase
{
    private $transformation;
    private $transformer;

    protected function setUp()
    {
        $this->transformation = $this->getMock('AntiMattr\ETL\Transform\TransformationInterface');
        $this->transformer = new MongoEmbedOneTransformer();
        $this->transformer->options['field'] = 'foo';
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Transform\Transformer\TransformerInterface', $this->transformer);
    }

    /**
     * @expectedException \AntiMattr\ETL\Exception\TransformException
     */
    public function testRequiredOptions()
    {
        $this->transformer = new MongoEmbedOneTransformer();
        $this->assertNull($this->transformer->transform(null, $this->transformation));
    }

    public function testNull()
    {
        $this->assertNull($this->transformer->transform(null, $this->transformation));
    }
}
