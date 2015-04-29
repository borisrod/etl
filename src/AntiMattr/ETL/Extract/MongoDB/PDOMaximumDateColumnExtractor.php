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
class PDOMaximumDateColumnExtractor extends PDOMaximumColumnExtractor
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
    protected function getMaximumValue(\PDOStatement $statement)
    {
        $result = $statement->fetchObject();
        if (!isset($result) || !isset($result->maximum)) {
            return new \MongoDate(strtotime($this->defaultValue));
        }

        if ($this->timezone) {
            $date = $result->maximum . ' ' . $this->timezone;
        } else {
            $date = $result->maximum;
        }

        return new \MongoDate(strtotime($date));
    }
}
