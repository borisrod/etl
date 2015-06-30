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
use AntiMattr\ETL\Transform\TransformationInterface;

class TransformationEvent extends Event
{
    protected $transformation;

    public function __construct(TransformationInterface $transformation)
    {
        $this->transformation = $transformation;
    }

    public function getTransformation()
    {
        return $this->transformation;
    }
}
