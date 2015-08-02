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

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class PDOMaximumColumnEmbedManyExtractor extends PDOMaximumColumnExtractor
{
    public function __construct(
        \MongoDB $db,
        $collection,
        $field,
        \PDO $connection,
        $table,
        $column,
        $criteria = '',
        $defaultValue = null,
        array $projection = [],
        array $sort = [],
        $embedManyField,
        $batchSize = null,
        $timeout = 30000)
    {
        $this->collection = $collection;
        $this->column = $column;
        $this->connection = $connection;
        $this->criteria = $criteria;
        $this->db = $db;
        $this->defaultValue = $defaultValue;
        $this->field = $field;
        $this->projection = $projection;
        $this->sort = $sort;
        $this->table = $table;
        $this->timeout = $timeout;
        $this->batchIterator = new MongoDBEmbedManyBatchIterator($embedManyField, $batchSize);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        $sql = sprintf(
            "select max(%s) as 'minimum' from %s %s;",
            $this->column,
            $this->table,
            $this->criteria
        );
        $statement = $this->connection->query($sql);
        $collection = $this->db->{$this->collection};
        $minValue = $this->getMinimumValue($statement);
        $sort = (empty($this->sort)) ? [ $this->field => 1 ] : $this->sort;

        if ($minValue) {

            $fieldCriteria = [ '$gt' => $minValue ];
            $maxValue = $this->getMaximumValue($statement);
            if ($maxValue) {
                $fieldCriteria['$lte'] = $maxValue;
            }

            $cursor = $collection
                ->find([$this->field => $fieldCriteria ], $this->projection)
                ->sort($sort)
                ->timeout($this->timeout);
        } else {
            $cursor = $collection->find([], $this->projection)->sort($sort)->timeout($this->timeout);
        }

        $this->batchIterator->setInnerIterator($cursor);
        return $this->batchIterator;
    }
}
