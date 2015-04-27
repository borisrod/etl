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
class MongoEmbedManyGetOneOrEmbedOneTransformer implements TransformerInterface
{
    const FIRST = '__first';
    const LAST = '__last';

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
        if (!isset($this->options['iteration'])) {
            throw new TransformException("MongoEmbedManyGetOneOrEmbedOneTransformer: Required $this->options['iteration']");
        }
        if (!isset($this->options['field'])) {
            throw new TransformException("MongoEmbedManyGetOneOrEmbedOneTransformer: Required $this->options['field']");
        }
        if (!isset($this->options['embed'])) {
            throw new TransformException("MongoEmbedManyGetOneOrEmbedOneTransformer: Required $this->options['embed']");
        }
        if (!isset($this->options['embed_field'])) {
            throw new TransformException("MongoEmbedManyGetOneOrEmbedOneTransformer: Required $this->options['embed_field']");
        }

        if ($foundValue = $this->getOneFromEmbedMany($value, $transformation)) {
            return $foundValue;
        }

        if ($foundValue = $this->getOneFromEmbedMany($value, $transformation)) {
            return $foundValue;
        }

        return isset($this->options['default']) ? $this->options['default'] : null;
    }

    /**
     * @param mixed                                                        $value
     * @param \AntiMattr\ETL\Transform\Transformer\TransformationInterface $transformation
     *
     * @return mixed $value
     */
    protected function getOneFromEmbedMany($value, TransformationInterface $transformation)
    {
        if (!isset($value)) {
            return;
        }

        if (!is_array($value) && !$value instanceof \Traversable) {
            return;
        }

        if (static::FIRST === $this->options['iteration']) {
            $value = array_shift($value);
        } elseif (static::LAST === $this->options['iteration']) {
            $value = array_pop($value);
        } else {
            $value = $value[$this->options['iteration']];
        }

        if (!isset($value[$this->options['field']]) ) {
            return;
        }

        return $value[$this->options['field']];
    }

    /**
     * @param mixed                                                        $value
     * @param \AntiMattr\ETL\Transform\Transformer\TransformationInterface $transformation
     *
     * @return mixed $value
     */
    protected function getValueFromEmbedOne($value, TransformationInterface $transformation)
    {
        $task = $transformation->getTask();
        $data = $task->getData();
        $currentExtractedRecord = $data->setCurrentExtractedRecord();

        if (!isset($currentExtractedRecord[$this->options['embed']]) ) {
            return;
        }

        $embed = $currentExtractedRecord[$this->options['embed']];

        if (!isset($embed[$this->options['embed_field']]) ) {
            return;
        }

        return $embed[$this->options['embed_field']];
    }
}
