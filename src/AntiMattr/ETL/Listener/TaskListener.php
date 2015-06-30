<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Listener;

use AntiMattr\ETL\Event\TaskEvent;

class TaskListener implements TaskListenerInterface
{
    /**
     * @param AntiMattr\ETL\Event\TaskEvent
     */
    public function onComplete(TaskEvent $event)
    {
        $task = $event->getTask();
        $loader = $task->getLoader();
        $dataContext = $task->getDataContext();
        $target = $dataContext->getTransformed();
        $loader->load($target);
        $dataContext->setEndedAt(new \DateTime());
    }
}
