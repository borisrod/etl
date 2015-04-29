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

use AntiMattr\ETL\Task\TaskInterface;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
trait ExtractorTrait
{
    /** @var \Doctrine\Common\Collections\Collection */
    protected $pages;

    /** @var integer */
    protected $perPage;

    /** @var \AntiMattr\ETL\Task\TaskInterface */
    protected $task;

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPages()
    {
        return $this->pages;
    }

    /**
     * @param integer $perPage
     */
    public function setPerPage($perPage)
    {
        $this->perPage = $perPage;
    }

    /**
     * @param \AntiMattr\ETL\Task\TaskInterface
     */
    public function setTask(TaskInterface $task)
    {
        $this->task = $task;
    }

    /**
     * @return \AntiMattr\ETL\Task\TaskInterface
     */
    public function getTask()
    {
        return $this->task;
    }

    /**
     * @param array $results
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    protected function createArrayCollection(array $data = [])
    {
        return new ArrayCollection($data);
    }
}
