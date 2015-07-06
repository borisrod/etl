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

/**
 * @author Matthew Fitzgerald <matthewfitz@gmail.com>
 */
class PDOMaximumColumnDateExtractor extends PDOMaximumColumnExtractor
{
    /**
     * @var string
     */
    protected $timezone;

    /**
     * @param string $timezone
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone;
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return \MongoDate
     */
    protected function getMinimumValue(\PDOStatement $statement)
    {
        $result = $statement->fetchObject();
        if (!isset($result) || !isset($result->minimum)) {
            return new \MongoDate(strtotime($this->defaultValue));
        }

        if ($this->timezone) {
            $date = $result->minimum . ' ' . $this->timezone;
        } else {
            $date = $result->minimum;
        }

        return new \MongoDate(strtotime($date));
    }

    /**
     * @param \PDOStatement $statement
     *
     * @return mixed $value
     */
    protected function getMaximumValue(\PDOStatement $statement)
    {
        return new \MongoDate();
    }
}
