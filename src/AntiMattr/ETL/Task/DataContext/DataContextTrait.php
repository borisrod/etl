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
trait DataContextTrait
{
    /** @var array */
    protected $currentExtractedRecord = [];

    /** @var array */
    protected $currentTransformedRecord = [];

    /** @var integer */
    protected $currentIteration;

    /** @var \DateTime */
    protected $endedAt;

    /** @var integer */
    protected $extractedCount;

    /** @var array */
    protected $extracted = [];

    /** @var integer */
    protected $loadedCount;

    /** @var \DateTime */
    protected $startedAt;

    /** @var array */
    protected $transformed = [];

    /** @var \AntiMattr\ETL\Task\TaskInterface */
    protected $task;

    public function __clone()
    {
        $this->currentExtractedRecord = [];
        $this->currentTransformedRecord = [];
        $this->currentIteration = null;
        $this->extractedCount;
        $this->extracted = [];
        $this->loadedCount;
        $this->transformed = [];
    }

    /**
     * @param array $currentExtractedRecord
     */
    public function setCurrentExtractedRecord(array $currentExtractedRecord = [])
    {
        $this->currentExtractedRecord = $currentExtractedRecord;
    }

    /**
     * @return array $currentExtractedRecord
     */
    public function getCurrentExtractedRecord()
    {
        return $this->currentExtractedRecord;
    }

    /**
     * @param array $currentTransformedRecord
     */
    public function setCurrentTransformedRecord(array $currentTransformedRecord = [])
    {
        $this->currentTransformedRecord = $currentTransformedRecord;
    }

    /**
     * @return array $currentTransformedRecord
     */
    public function getCurrentTransformedRecord()
    {
        return $this->currentTransformedRecord;
    }

    /**
     * @param integer $currentIteration
     */
    public function setCurrentIteration($currentIteration)
    {
        $this->currentIteration = $currentIteration;
    }

    /**
     * @return integer $currentIteration
     */
    public function getCurrentIteration()
    {
        return $this->currentIteration;
    }

    /**
     * @param \DateTime $endedAt
     */
    public function setEndedAt(\DateTime $endedAt)
    {
        $this->endedAt = $endedAt;
    }

    /**
     * @return \DateTime $endedAt
     */
    public function getEndedAt()
    {
        return $this->endedAt;
    }

    /**
     * @param array $extracted
     */
    public function setExtracted(array $extracted = [])
    {
        $this->extracted = $extracted;
        $this->extractedCount = count($extracted);
    }

    /**
     * @param integer $extractedCount
     */
    public function getExtractedCount()
    {
        return $this->extractedCount;
    }

    /**
     * @return array $extracted
     */
    public function getExtracted()
    {
        return $this->extracted;
    }

    /**
     * @param integer $loadedCount
     */
    public function getLoadedCount()
    {
        return $this->loadedCount;
    }

    /**
     * @param integer $loadedCount
     */
    public function setLoadedCount($loadedCount)
    {
        $this->loadedCount = $loadedCount;
    }

    /**
     * @param \DateTime $startedAt
     */
    public function setStartedAt(\DateTime $startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return \DateTime $startedAt
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * @param array $extracted
     */
    public function setTransformed(array $transformed = [])
    {
        $this->transformed = $transformed;
    }

    /**
     * @param integer $transformedCount
     */
    public function getTransformedCount()
    {
        return count($this->transformed);
    }

    /**
     * @return array $transformed
     */
    public function getTransformed()
    {
        return $this->transformed;
    }

    /**
     * @param array $extracted
     */
    public function mergeTransformed(array $transformed = [])
    {
        $this->transformed = array_merge($this->transformed, $transformed);
    }

    /**
     * @param string $key
     */
    public function unsetExtractedOffset($key)
    {
        unset($this->extracted[$key]);
    }

    /**
     * @param string $key
     */
    public function unsetTransformedOffset($key)
    {
        unset($this->transformed[$key]);
    }

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }
}
