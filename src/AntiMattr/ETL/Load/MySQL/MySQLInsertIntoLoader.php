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
use AntiMattr\ETL\Load\LoaderInterface;
use AntiMattr\ETL\Load\LoaderTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MySQLInsertIntoLoader implements LoaderInterface
{
    use LoaderTrait;

    /** @var \PDO */
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
            throw new LoadException("Error - No data to load");
        }

        $dataContext = $this->task->getDataContext();

        $firstRow = array_slice($transformed, 0, 1);
        $first = array_shift($firstRow);
        $properties = array_keys($first);
        $columns = implode(', ', $properties);

        $valuePlaceholders = [];
        $values = [];
        foreach ($transformed as $row) {
            $result = [];
            $count = sizeof($row);
            if ($count > 0) {
                $values = array_merge($values, array_values($row));
                for($x = 0; $x < $count; $x++){
                    $result[] = '?';
                }
            }

            $valuePlaceholders[] = '(' . implode(',', $result) . ')';
        }

        $sql = sprintf(
            "INSERT INTO %s (%s) VALUES %s",
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

            throw new LoadException($e->getMessage());
        }

        $dataContext->setLoadedCount($statement->rowCount());
    }
}
