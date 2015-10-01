<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Load\MySQL;

use AntiMattr\ETL\Exception\LoadException;
use AntiMattr\ETL\Exception\LoadNoDataException;
use AntiMattr\ETL\Load\LoaderInterface;
use AntiMattr\ETL\Load\LoaderTrait;
use AntiMattr\ETL\Load\PDO\PDOStatement;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MySQLReplaceIntoLoader implements LoaderInterface
{
    use LoaderTrait;

    /** @var string */
    protected $connection;

    /** @var string */
    protected $table;

    public function __construct(\PDO $connection, $table)
    {
        $this->connection = $connection;
        $this->table = $table;
    }

    /**
     * @param array $transformed
     *
     * @throws \AntiMattr\ETL\Exception\LoadException
     */
    public function load(array $transformed = [])
    {
        if (empty($transformed)) {
            throw new LoadNoDataException("Error - No data to load");
        }

        $properties = [];
        $values = [];

        $transformations = $this->task->getTransformations();

        foreach ($transformations as $transformation) {
            $properties[$transformation->name] = null;
        }

        $columns = implode(', ', array_keys($properties));
        $questionMarks = array_fill(0, count($properties), '?');
        $valuePlaceholders = array_fill(0, count($transformed), '(' . implode(',', $questionMarks) . ')');

        foreach ($transformed as $row) {
            $value = array_merge($properties, $row);
            $values = array_merge($values, array_values($value));
        }

        $sql = sprintf(
            "REPLACE INTO %s (%s) VALUES %s",
            $this->table,
            $columns,
            implode(',', $valuePlaceholders)
        );

        $this->connection->beginTransaction();
        try {
            $statement = $this->connection->prepare($sql);
            $statement->execute($values);
            $this->connection->commit();
        } catch (\PDOException $e){
            try {
                $this->connection->rollBack();
            } catch (\Exception $rollback) {

            }

            $query = $statement->queryString;
            if ($statement instanceof PDOStatement) {
                $query = $statement->getDebugQuery();
            }

            $message = sprintf(
                "Error: %s Query: %s",
                $e->getMessage(),
                $query
            );

            throw new LoadException($message);
        }

        $dataContext = $this->task->getDataContext();
        $dataContext->setLoadedCount($statement->rowCount());
    }
}
