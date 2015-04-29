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
class PDOMaximumColumnExtractor extends MongoDBExtractor
{
    /** @var string */
    protected $column;

    /** @var mixed */
    protected $defaultValue;

    /** @var string */
    protected $field;

    public function __construct(
        \MongoDB $db,
        $collection,
        $field,
        \PDO $connection,
        $table,
        $column,
        $defaultValue = null,
        array $projection = [])
    {
        $this->collection = $collection;
        $this->column = $column;
        $this->connection = $connection;
        $this->db = $db;
        $this->defaultValue = $defaultValue;
        $this->field = $field;
        $this->projection = $projection;
        $this->table = $table;
    }

    public function getPages()
    {
        $sql = sprintf(
            "select max(%s) as 'maximum' from %s;",
            $this->column,
            $this->table
        );
        $statement = $this->connection->query($sql);
        $collection = $this->db->{$this->collection};
        $value = $this->getMaximumValue($statement);

        if ($value) {
            $cursor = $collection->find([$this->field => [ '$gt' => $value ] ], $this->projection);
        } else {
            $cursor = $collection->find([], $this->projection);
        }

        $this->buildPagesFromCursor($cursor);

        return $this->pages;
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
