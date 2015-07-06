<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL;

use AntiMattr\ETL\Lock\LockInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class ProcessorFactory
{
    /**
     * @param string                                                      $alias
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \AntiMattr\ETL\Lock\LockInterface                           $locker
     * @param \Psr\Log\LoggerInterface                                    $logger
     *
     * @return AntiMattr\ETL\Processor
     */
    static public function getProcessor($alias, EventDispatcherInterface $eventDispatcher, LockInterface $locker, LoggerInterface $logger = null)
    {
        return new Processor($alias, $eventDispatcher, $locker, $logger);
    }
}
