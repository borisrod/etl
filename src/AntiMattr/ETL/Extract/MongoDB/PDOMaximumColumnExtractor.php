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
        $batchSize = null)
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
        $this->batchIterator = new BatchIterator($batchSize);
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        $sql = sprintf(
            "select max(%s) as 'maximum' from %s %s;",
            $this->column,
            $this->table,
            $this->criteria
        );
        $statement = $this->connection->query($sql);
        $collection = $this->db->{$this->collection};
        $value = $this->getMaximumValue($statement);
        $sort = (empty($this->sort)) ? [ $this->field => 1 ] : $this->sort;

        if ($value) {
            $cursor = $collection
                ->find([$this->field => [ '$gt' => $value ] ], $this->projection)
                ->sort($sort);
        } else {
            $cursor = $collection->find([], $this->projection)->sort($sort);
        }

        $this->batchIterator->setInnerIterator($cursor);
        return $this->batchIterator;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return mixed $value
     */
    protected function getMaximumValue(\PDOStatement $statement)
    {
        $result = $statement->fetchObject();
        if (!isset($result) || !isset($result->maximum)) {
            return $this->defaultValue;
        }

        return $result->maximum;
    }
}
