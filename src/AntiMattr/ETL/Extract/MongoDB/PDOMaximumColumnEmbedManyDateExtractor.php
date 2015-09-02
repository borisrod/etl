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
class PDOMaximumColumnEmbedManyDateExtractor extends PDOMaximumColumnEmbedManyExtractor
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
            $date = new \DateTime($this->defaultValue);
        } else {
            $date = new \DateTime($result->minimum);
        }

        if ($this->timezone) {
            $date->setTimezone(new \DateTimeZone($this->timezone));
        }

        return new \MongoDate($date->getTimestamp());
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
