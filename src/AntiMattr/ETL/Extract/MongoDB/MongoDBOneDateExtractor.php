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
class MongoDBOneDateExtractor extends MongoDBExtractor
{
    /** @var string */
    protected $field;

    /** @var string */
    protected $modify;

    public function __construct(
        \MongoDB $db,
        $collection,
        $field,
        $modify,
        array $projection = [],
        array $sort = [],
        $batchSize = null)
    {
        $this->collection = $collection;
        $this->db = $db;
        $this->field = $field;
        $this->modify = $modify;
        $this->projection = $projection;
        $this->sort = $sort;
        $this->batchIterator = new BatchIterator($batchSize);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        $dateMinimum = $this->createDateTime();
        $dateMinimum->modify($this->modify);
        $dateMinimum->setTime(0, 0, 0);

        $min = $this->createMongoDate($dateMinimum->getTimestamp());

        $dateMaximum = clone $dateMinimum;
        $dateMaximum->setTime(23, 59, 59);

        $max = $this->createMongoDate($dateMaximum->getTimestamp());

        $fieldCriteria = [
            $this->field => [ '$gt' => $min, '$lte' => $max ]
        ];

        $cursor = $this->db->{$this->collection}->find($fieldCriteria, $this->projection)->sort($this->sort);
        $this->batchIterator->setInnerIterator($cursor);
        return $this->batchIterator;
    }

    /**
     * @return \DateTime
     */
    protected function createDateTime()
    {
        return new \DateTime();
    }

    /**
     * @return \MongoDate
     */
    protected function createMongoDate($timestamp)
    {
        return new \MongoDate($timestamp);
    }
}
