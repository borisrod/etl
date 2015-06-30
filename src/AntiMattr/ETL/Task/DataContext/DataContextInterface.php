<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Task\DataContext;

use AntiMattr\ETL\Task\TaskInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface DataContextInterface
{
    /**
     * @param array $currentExtractedRecord
     */
    public function setCurrentExtractedRecord(array $currentExtractedRecord = []);

    /**
     * @param return $currentExtractedRecord
     */
    public function getCurrentExtractedRecord();

    /**
     * @param integer $currentIteration
     */
    public function setCurrentIteration($currentIteration);

    /**
     * @return integer $currentIteration
     */
    public function getCurrentIteration();

    /**
     * @param \DateTime $endedAt
     */
    public function setEndedAt(\DateTime $endedAt);

    /**
     * @return \DateTime $endedAt
     */
    public function getEndedAt();

    /**
     * @param array $extracted
     */
    public function setExtracted(array $extracted = []);

    /**
     * @param integer $extractedCount
     */
    public function getExtractedCount();

    /**
     * @return array $extracted
     */
    public function getExtracted();

    /**
     * @param integer $loadedCount
     */
    public function getLoadedCount();

    /**
     * @param integer $loadedCount
     */
    public function setLoadedCount($loadedCount);

    /**
     * @param \DateTime $startedAt
     */
    public function setStartedAt(\DateTime $startedAt);

    /**
     * @return \DateTime $startedAt
     */
    public function getStartedAt();

    /**
     * @param array $extracted
     */
    public function setTransformed(array $transformed = []);

    /**
     * @param integer $transformedCount
     */
    public function getTransformedCount();

    /**
     * @return array $transformed
     */
    public function getTransformed();

    /**
     * @param array $extracted
     */
    public function mergeTransformed(array $transformed = []);

    /**
     * @param string $key
     */
    public function unsetTransformedOffset($key);

    /**
     * @param string $key
     */
    public function unsetExtractedOffset($key);

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task);

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask();
}
