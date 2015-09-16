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

use AntiMattr\ETL\Exception\TransformException;
use AntiMattr\ETL\Transform\TransformationInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerInterface;
use AntiMattr\ETL\Transform\Transformer\TransformerTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoDBRefTransformer implements TransformerInterface
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
        if (null === $value) {
            return;
        }

        if (is_array($value) && isset($value['$id'])) {
            return (string) $value['$id'];
        } elseif ($value instanceof \MongoDBRef) {
            return (string) $value['$id'];
        } elseif ($value instanceof \MongoId) {
            return (string) $value;
        }

        $type = true === is_object($value) ? get_class($value) : gettype($value);
        $valueString = is_array($value) ? implode(',', $value) : (string) $value;

        $message = sprintf(
            "MongoDBRefTransformer: Value % is of type %s and cannot be transformed",
            $valueString,
            $type
        );
        throw new TransformException($message);
    }
}
