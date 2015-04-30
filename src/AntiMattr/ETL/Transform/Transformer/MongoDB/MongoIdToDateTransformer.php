<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Transform\Transformer\MongoDB;

use AntiMattr\ETL\Transform\TransformationInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoIdToDateTransformer implements TransformerInterface
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
        if (!isset($value) || !$value instanceof \MongoId) {
            return isset($this->options['default']) ? $this->options['default'] : null;
        }

        $date = new \DateTime();
        $date->setTimestamp($value->getTimestamp());

        if (isset($this->options['timezone'])) {
            $date->setTimezone(new \DateTimeZone($this->options['timezone']));
        }

        $format = isset($this->options['format']) ? $this->options['format'] : 'Y-m-d H:i:s';
        return $date->format($format);
    }
}
