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

use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoDBExtractor implements ExtractorInterface
{
    use ExtractorTrait;

    /** @var string */
    protected $collection;

    /** @var MongoDB */
    protected $db;

    /** @var array */
    protected $query = [];

    /** @var array */
    protected $projection = [];

    public function __construct(\MongoDB $db, $collection, array $query = [], array $projection = [])
    {
        $this->collection = $collection;
        $this->db = $db;
        $this->query = $query;
        $this->pages = new ArrayCollection();
        $this->projection = $projection;
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        $results = iterator_to_array($this->db->{$this->collection}->find($this->query, $this->projection));

        if (!isset($this->perPage)) {
            $this->pages->add($results);
        } else {
            $this->pages = new ArrayCollection(array_chunk($results, $this->perPage, true));
        }

        return $this->pages;
    }
}
