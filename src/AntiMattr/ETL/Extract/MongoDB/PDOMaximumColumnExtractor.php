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

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class PDOMaximumColumnExtractor extends MongoDBExtractor
{
    /** @var string */
    protected $column;

    /** @var \PDO */
    protected $connection;

    /** @var string */
    protected $criteria;

    /** @var mixed */
    protected $defaultValue;

    /** @var string */
    protected $field;

    /** @var string */
    protected $table;

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
        $this->batchIterator = new BatchIterator($batchSize);
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
                ->timeout($timeout);
        } else {
            $cursor = $collection->find([], $this->projection)->sort($sort)->timeout($this->timeout);
        }

        $this->batchIterator->setInnerIterator($cursor);
        return $this->batchIterator;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return mixed $value
     */
    protected function getMinimumValue(\PDOStatement $statement)
    {
        $result = $statement->fetchObject();
        if (!isset($result) || !isset($result->minimum)) {
            return $this->defaultValue;
        }

        return $result->minimum;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return mixed $value
     */
    protected function getMaximumValue(\PDOStatement $statement)
    {
        return;
    }
}
