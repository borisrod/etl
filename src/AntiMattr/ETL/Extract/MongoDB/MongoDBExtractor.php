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

use AntiMattr\ETL\Extract\ExtractorInterface;
use AntiMattr\ETL\Extract\ExtractorTrait;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class MongoDBExtractor implements ExtractorInterface
{
    use ExtractorTrait;

    /** @var string */
    protected $collection;

    /** @var \MongoDB */
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
        $this->projection = $projection;
    }

    public function getPages()
    {
        $cursor = $this->db->{$this->collection}->find($this->query, $this->projection);
        $this->buildPagesFromCursor($cursor);

        return $this->pages;
    }

    /**
     * @param \MongoCursor $cursor
     */
    protected function buildPagesFromCursor(\MongoCursor $cursor)
    {
        $results = iterator_to_array($cursor);

        if (!isset($this->perPage)) {
            $this->pages = $this->createArrayCollection($results);
        } else {
            $this->pages = $this->createArrayCollection(array_chunk($results, $this->perPage, true));
        }
    }
}
