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

use Psr\Log\LoggerInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class ProcessorFactory
{
    /**
     * @param string                   $alias
     * @param \Psr\Log\LoggerInterface $logger
     *
     * @return AntiMattr\ETL\Processor
     */
    static public function getProcessor($alias, LoggerInterface $logger = null)
    {
        return new Processor($alias, $logger);
    }
}
