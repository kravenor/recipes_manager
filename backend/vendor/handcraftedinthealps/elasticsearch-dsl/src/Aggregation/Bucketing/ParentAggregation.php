<?php

/*
 * This file is part of the ONGR package.
 *
 * (c) NFQ Technologies UAB <info@nfq.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ONGR\ElasticsearchDSL\Aggregation\Bucketing;

use ONGR\ElasticsearchDSL\Aggregation\AbstractAggregation;
use ONGR\ElasticsearchDSL\Aggregation\Type\BucketingTrait;

/**
 * Class representing ChildrenAggregation.
 *
 * @link https://www.elastic.co/guide/en/elasticsearch/reference/current/search-aggregations-bucket-children-aggregation.html
 */
class ParentAggregation extends AbstractAggregation
{
    use BucketingTrait;

    /**
     * @var string
     */
    private $parent;

    /**
     * Return children.
     *
     * @return string
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $name
     * @param string $parent
     */
    public function __construct($name, $parent = null)
    {
        parent::__construct($name);

        $this->setParent($parent);
    }

    /**
     * @param string $parent
     *
     * @return $this
     */
    public function setParent($parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'parent';
    }

    /**
     * {@inheritdoc}
     */
    public function getArray()
    {
        if (count($this->getAggregations()) == 0) {
            throw new \LogicException("Parent aggregation `{$this->getName()}` has no aggregations added");
        }

        return ['type' => $this->getParent()];
    }
}
