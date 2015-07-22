<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\BatchIterator;
use AntiMattr\ETL\Extract\ExtractorInterface;
use AntiMattr\ETL\Extract\ExtractorTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoDBExtractor implements ExtractorInterface
{
    use ExtractorTrait;

    /** @var string */
    protected $collection;

    /** @var \MongoDB */
    protected $db;

    /** @var array */
    protected $query = [];

    /** @var array */
    protected $projection = [];

    /** @var array */
    protected $sort = [];

    public function __construct(
        \MongoDB $db,
        $collection,
        array $query = [],
        array $projection = [],
        array $sort = [],
        $batchSize = null)
    {
        $this->collection = $collection;
        $this->db = $db;
        $this->query = $query;
        $this->projection = $projection;
        $this->sort = $sort;
        $this->batchIterator = new BatchIterator($batchSize);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        $cursor = $this->db->{$this->collection}->find($this->query, $this->projection)->sort($this->sort);
        $this->batchIterator->setInnerIterator($cursor);
        return $this->batchIterator;
    }
}
