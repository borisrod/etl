<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Transform\Transformer;

use AntiMattr\ETL\Transform\TransformationInterface;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait TransformerTrait
{
    /** @var array */
    public $options = [];

    /**
     * @param mixed                                            $value
     * @param \AntiMattr\ETL\Transform\TransformationInterface $transformation
     */
    public function bind($value, TransformationInterface $transformation)
    {
        $task = $transformation->getTask();
        $data = $task->getData();
        $name = $transformation->getName();
        $currentTransformedRecord = $data->getCurrentTransformedRecord();
        $currentTransformedRecord[$name] = $value;
        $data->setCurrentTransformedRecord($currentTransformedRecord);
    }
}
