<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Tests\Unit\Aggregation\Bucketing;

use ONGR\ElasticsearchDSL\Aggregation\Bucketing\ParentAggregation;

/**
 * Unit test for parent aggregation.
 */
class ParentAggregationTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests if ParentAggregation#getArray throws exception when expected.
     */
    public function testGetArrayException()
    {
        $this->expectException(\LogicException::class);
        $aggregation = new ParentAggregation('foo');
        $aggregation->getArray();
    }

    /**
     * Tests getType method.
     */
    public function testParentAggregationGetType()
    {
        $aggregation = new ParentAggregation('foo');
        $result = $aggregation->getType();
        $this->assertEquals('parent', $result);
    }

    /**
     * Tests getArray method.
     */
    public function testParentAggregationGetArray()
    {
        $mock = $this->getMockBuilder('ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation')
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $aggregation = new ParentAggregation('foo');
        $aggregation->addAggregation($mock);
        $aggregation->setParent('question');
        $result = $aggregation->getArray();
        $expected = ['type' => 'question'];
        $this->assertEquals($expected, $result);
    }
}
