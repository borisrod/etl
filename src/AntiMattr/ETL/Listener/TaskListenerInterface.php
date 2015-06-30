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

interface TaskListenerInterface
{
    /**
     * @param AntiMattr\ETL\Event\TaskEvent
     */
    public function onComplete(TaskEvent $event);
}
