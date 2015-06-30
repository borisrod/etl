<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Event;

use Symfony\Component\EventDispatcher\Event;
use AntiMattr\ETL\Task\TaskInterface;

class TaskEvent extends Event
{
    protected $task;

    public function __construct(TaskInterface $task)
    {
        $this->task = $task;
    }

    public function getTask()
    {
        return $this->task;
    }
}
