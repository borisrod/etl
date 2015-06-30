<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Load;

use AntiMattr\ETL\Task\TaskInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface LoaderInterface
{
    /**
     * @param array $transformed
     *
     * @throws \AntiMattr\ETL\Exception\LoadException
     */
    public function load(array $transformed = []);

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task);

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask();
}
