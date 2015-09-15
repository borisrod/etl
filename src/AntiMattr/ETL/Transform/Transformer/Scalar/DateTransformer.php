<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Transform\Transformer\Scalar;

use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Transform\TransformationInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class DateTransformer implements TransformerInterface
{
    use TransformerTrait;

    /**
     * @param mixed                                                        $value
     * @param \AntiMattr\ETL\Transform\Transformer\TransformationInterface $transformation
     *
     * @return mixed $value
     *
     * @throws \AntiMattr\ETL\Exception\TransformException
     */
    public function transform($value, TransformationInterface $transformation)
    {
        if (!isset($value) || !is_string($value)) {
            return null;
        }

        if (isset($this->options['timezone'])) {
            $timezone = new \DateTimeZone($this->options['timezone']);
            $date = new \DateTime($value, $timezone);
        } else {
            $date = new \DateTime($value);
        }

        $format = isset($this->options['format']) ? $this->options['format'] : 'Y-m-d H:i:s';
        return $date->format($format);
    }
}
