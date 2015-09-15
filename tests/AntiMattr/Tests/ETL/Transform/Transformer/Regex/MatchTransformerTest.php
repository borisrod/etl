<?php

namespace AntiMattr\Tests\ETL\Transform\Transformer\Regex;

use AntiMattr\ETL\Transform\Transformer\Regex\MatchTransformer;
use AntiMattr\TestCase\AntiMattrTestCase;

class MatchTransformerTest extends AntiMattrTestCase
{
    private $transformation;
    private $transformer;

    protected function setUp()
    {
        $this->transformation = $this->getMock('AntiMattr\ETL\Transform\TransformationInterface');
        $this->transformer = new MatchTransformer();
        $this->transformer->options['pattern'] = "/^.{5}[02468ace]/";
        $this->transformer->options['true'] = "a";
        $this->transformer->options['false'] = "b";
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
        $this->transformer = new MatchTransformer();
        $this->assertNull($this->transformer->transform(null, $this->transformation));
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
        $string = '4e599a80b1f9644720000005';
        $transformed = $this->transformer->transform($string, $this->transformation);
        $this->assertEquals('a', $transformed);

        $string = '4d9351b2f4d505377f000800';
        $transformed = $this->transformer->transform($string, $this->transformation);
        $this->assertEquals('b', $transformed);
    }
}
