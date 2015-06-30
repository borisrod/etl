<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Extract;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class BatchIterator implements \Iterator
{
    /** @var int */
    protected $batchSize;

    /** @var int */
    protected $count = 0;

    /** @var \Iterator */
    protected $innerIterator;

    public function __construct($batchSize = null)
    {
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

        if (($this->count+1) % $this->batchSize === 0) {
            return true;
        }

        return false;
    }

    /**
     * @return \Iterator
     */
    public function setInnerIterator(\Iterator $innerIterator)
    {
        $this->innerIterator = $innerIterator;
    }

    /**
     * @see \Iterator
     */
    public function current()
    {
        return $this->innerIterator->current();
    }

    /**
     * @see \Iterator
     */
    public function next()
    {
        $this->count++;
        return $this->innerIterator->next();
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
