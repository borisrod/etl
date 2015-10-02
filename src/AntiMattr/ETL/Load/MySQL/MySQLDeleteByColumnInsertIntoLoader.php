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
use AntiMattr\ETL\Load\PDO\PDOStatement;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MySQLDeleteByColumnInsertIntoLoader extends MySQLReplaceIntoLoader
{
    /** @var string */
    protected $column;

    public function __construct(\PDO $connection, $table, $column)
    {
        $this->connection = $connection;
        $this->table = $table;
        $this->column = $column;
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

        $foreignKeys = [];
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
            if (!isset($row[$this->column])) {
                continue;
            }

            $foreignKeys[] = $row[$this->column];

            $value = array_merge($properties, $row);
            $values = array_merge($values, array_values($value));
        }

        $foreignKeys = array_unique($foreignKeys);

        $loadedCount = 0;

        if (count($foreignKeys) > 0) {
            $deleteSql = sprintf(
                "DELETE FROM %s WHERE %s IN(%s);",
                $this->table,
                $this->column,
                implode(',', array_map(function($id){
                    return sprintf("'%s'", $id);
                }, $foreignKeys))
            );

            $this->connection->beginTransaction();
            try {
                $delete = $this->connection->prepare($deleteSql);
                $delete->execute();
                $this->connection->commit();
                $loadedCount += $delete->rowCount();
            } catch (\PDOException $e){
                try {
                    $this->connection->rollBack();
                } catch (\Exception $rollback) {

                }

                $query = $delete->queryString;
                if ($delete instanceof PDOStatement) {
                    $query = $delete->getDebugQuery();
                }

                $message = sprintf(
                    "Error: %s Query: %s",
                    $e->getMessage(),
                    $query
                );

                throw new LoadException($message);
            }
        }

        $insertSql = sprintf(
            "INSERT INTO %s (%s) VALUES %s;",
            $this->table,
            $columns,
            implode(',', $valuePlaceholders)
        );

        $this->connection->beginTransaction();
        try {
            $insert = $this->connection->prepare($insertSql);
            $insert->execute($values);
            $this->connection->commit();
            $loadedCount += $insert->rowCount();
        } catch (\PDOException $e){
            try {
                $this->connection->rollBack();
            } catch (\Exception $rollback) {

            }

            $query = $insert->queryString;
            if ($insert instanceof PDOStatement) {
                $query = $insert->getDebugQuery();
            }

            $message = sprintf(
                "Error: %s Query: %s",
                $e->getMessage(),
                $query
            );

            throw new LoadException($message);
        }

        $dataContext = $this->task->getDataContext();
        $dataContext->setLoadedCount($loadedCount);
    }
}
