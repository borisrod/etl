<?php

namespace AntiMattr\Tests\ETL\Transform\Transformer\Scalar;

use AntiMattr\ETL\Transform\Transformer\Scalar\DateTransformer;
use AntiMattr\TestCase\AntiMattrTestCase;

class DateTransformerTest extends AntiMattrTestCase
{
    private $transformation;
    private $transformer;

    protected function setUp()
    {
        $this->transformation = $this->getMock('AntiMattr\ETL\Transform\TransformationInterface');
        $this->transformer = new DateTransformer();
    }

    public function testConstructor()
    {
        $this->assertInstanceOf('AntiMattr\ETL\Transform\Transformer\TransformerInterface', $this->transformer);
    }

    public function testNull()
    {
        $this->assertNull($this->transformer->transform(null, $this->transformation));
    }

    public function testTransformNotString()
    {
        $this->assertNull($this->transformer->transform([], $this->transformation));
    }

    public function testTransform()
    {
        $dateString = "06/29/1967";
        $this->assertEquals("1967-06-29 00:00:00", $this->transformer->transform($dateString, $this->transformation));
    }

    public function testTransformWithTimezone()
    {
        $this->transformer = new DateTransformer();
        $this->transformer->options['timezone'] = 'America/Los_Angeles';
        $this->transformer->options['format'] = 'Y-m-d H:i:s O';

        $dateString = "06/29/1967";
        $this->assertEquals("1967-06-29 00:00:00 -0700", $this->transformer->transform($dateString, $this->transformation));
    }
}
