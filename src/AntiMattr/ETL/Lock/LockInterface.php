<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Lock;

use AntiMattr\ETL\Processor;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
interface LockInterface
{
    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function hasLock(Processor $processor, $taskName);

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function lock(Processor $processor, $taskName);

    /**
     * @param \AntiMattr\ETL\Processor $processor
     * @param string                   $taskName
     */
    public function unlock(Processor $processor, $taskName);
}
