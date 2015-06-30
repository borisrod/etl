<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Extract;

use AntiMattr\ETL\Task\TaskInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait ExtractorTrait
{
    /** @var \AntiMattr\ETL\Extract\BatchIterator */
    protected $batchIterator;

    /** @var \AntiMattr\ETL\Task\TaskInterface */
    protected $task;

    /**
     * @return \AntiMattr\ETL\Extract\BatchIterator
     */
    public function getIterator()
    {
        return $this->batchIterator;
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
