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
    /**
     * @var string
     */
    protected $batchField;

    /**
     * @var string
     */
    protected $embedManyField;

    /**
     * @string $batchField
     */
    public function setBatchField($batchField)
    {
        $this->batchField = $batchField;
    }

    /**
     * @string $embedManyField
     */
    public function setEmbedManyField($embedManyField)
    {
        $this->embedManyField = $embedManyField;
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
     * @param \MongoCursor $cursor
     */
    protected function buildPagesFromCursor(\MongoCursor $cursor)
    {
        $results = iterator_to_array($cursor);
        $expandedResults = [];
        foreach($results as $record) {
            if (!isset($record[$this->embedManyField]) || empty($record[$this->embedManyField])) {
                continue;
            }

            foreach ($record[$this->embedManyField] as $embed) {
                $alteredRecord = $record;
                $alteredRecord[$this->embedManyField] = $embed;
                $expandedResults[] = $alteredRecord;
            }
        }

        if (!isset($this->perPage)) {
            $this->pages = $this->createArrayCollection($expandedResults);
        } elseif ($this->batchField) {
            $batches = [];
            $batchId = 0;
            $batchIteration = 0;
            $previousBatchId = null;
            $currentBatchId = null;

            foreach ($expandedResults as $key => $record) {
                if (!isset($batches[$batchId])) {
                    $batches[$batchId] = [];
                    $batchIteration = 0;
                    $previousBatchId = null;
                }

                $currentBatchId = $this->getNestedArrayValue($record, $this->batchField);

                if ($batchIteration < $this->perPage) {
                    $batches[$batchId][] = $record;
                    $batchIteration++;
                    $previousBatchId = $currentBatchId;
                    continue;
                }

                if ($previousBatchId == $currentBatchId) {
                    $batches[$batchId][] = $record;
                    $batchIteration++;
                    continue;
                }
                $batchId++;
                $batches[$batchId] = [];
                $batchIteration = 0;
                $batches[$batchId][] = $record;
                $previousBatchId = null;
            }

            $this->pages = $this->createArrayCollection($batches);
        } else {
            $this->pages = $this->createArrayCollection(array_chunk($results, $this->perPage, true));
        }
    }

    /**
     * Ex: 'attributes._id'
     *
     * @param array $record
     * @param string $selector
     *
     * @return mixed
     */
    protected function getNestedArrayValue($record, $selector)
    {
        $keys = explode('.', $selector);

        foreach ($keys as $key) {
            if (!is_array($record) || !isset($record[$key])) {
                break;
            }

            $record = $record[$key];
        }

        return $record;
    }
}
