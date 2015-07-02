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
    /** @var string */
    protected $embedManyField;

    public function __construct($embedManyField, $batchSize = null)
    {
        $this->embedManyField = $embedManyField;
        $this->batchSize = $batchSize;
    }

    /**
     * @return boolean
     */
    public function isBatchComplete()
    {
        if (null === $this->batchSize || 0 === $this->count) {
            return false;
        }

        if (($this->count + 1) % $this->batchSize === 0) {
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
        if (!isset($current[$this->embedManyField])) {
            $this->batchSize = null;
            $this->count = 0;
            if ($this->innerIterator->hasNext()) {
                $this->innerIterator->next();
                return $this->current();
            }
            return [];
        }

        $embed = array_values($current[$this->embedManyField]);
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
        $current = $this->innerIterator->current();

        if (!isset($current[$this->embedManyField])) {
            $this->count = 0;
            $this->innerIterator->next();
            return;
        }

        $embed = array_values($current[$this->embedManyField]);
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
        return $this->innerIterator->key();
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
        $this->innerIterator->rewind();
    }
}
