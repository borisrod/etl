<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Extract\MongoDB;

use AntiMattr\ETL\Extract\BatchIterator;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoDBEmbedManyBatchIterator extends BatchIterator
{
    /** @var integer */
    protected $key;

    /** @var string */
    protected $embedManyField;

    public function __construct($embedManyField, $batchSize = null)
    {
        $this->embedManyField = $embedManyField;
        $this->batchSize = $batchSize;
        $this->key = 0;
    }

    /**
     * @return boolean
     */
    public function isBatchComplete()
    {
        if (null === $this->batchSize || 0 === $this->count) {
            return false;
        }

        if (($this->count) % $this->batchSize === 0) {
            return true;
        }

        return false;
    }

    /**
     * @see \Iterator
     */
    public function current()
    {
        $current = $this->innerIterator->current();
        if (false === $this->hasEmbeddedProperty($current)) {
            $this->batchSize = null;
            $this->count = 0;
            if ($this->innerIterator->hasNext()) {
                $this->innerIterator->next();
                return $this->current();
            }
            return [];
        }

        $embed = array_values($this->getEmbeddedProperty($current));
        if (!isset($embed[$this->count])) {
            $this->batchSize = null;
            $this->count = 0;
            if ($this->innerIterator->hasNext()) {
                $this->innerIterator->next();
                return $this->current();
            }
            return [];
        }

        $this->batchSize = count($embed);
        $current[$this->embedManyField] = $embed[$this->count];
        $this->count++;

        return $current;
    }

    /**
     * @see \Iterator
     */
    public function next()
    {
        $this->key++;

        $current = $this->innerIterator->current();
        if (false === $this->hasEmbeddedProperty($current)) {
            $this->count = 0;
            $this->innerIterator->next();
            return;
        }

        $embed = array_values($this->getEmbeddedProperty($current));
        $size = count($embed);

        if ($this->count < $size) {
            return;
        }

        $this->count = 0;
        $this->innerIterator->next();
    }

    /**
     * @see \Iterator
     */
    public function key()
    {
        return $this->key;
    }

    /**
     * @see \Iterator
     */
    public function valid()
    {
        return $this->innerIterator->valid();
    }

    /**
     * @see \Iterator
     */
    public function rewind()
    {
        $this->count = 0;
        $this->key = 0;
        $this->innerIterator->rewind();
    }

    /**
     * @param array $current
     *
     * @return Boolean
     */
    protected function hasEmbeddedProperty(array $current = [])
    {
        if (false === strpos($this->embedManyField, '.')) {
            return isset($current[$this->embedManyField]);
        }

        $pieces = explode('.', $this->embedManyField);

        $chain = $current;
        foreach ($pieces as $piece) {
            if (!isset($chain[$piece])) {
                return false;
            }
            $chain = $chain[$piece];
        }

        return true;
    }

    /**
     * @param array $current
     *
     * @return mixed
     */
    protected function getEmbeddedProperty(array $current = [])
    {
        if (false === strpos($this->embedManyField, '.')) {
            return $current[$this->embedManyField];
        }

        $pieces = explode('.', $this->embedManyField);

        $chain = $current;
        foreach ($pieces as $piece) {
            if (!isset($chain[$piece])) {
                return;
            }
            $chain = $chain[$piece];
        }

        return $chain;
    }
}
