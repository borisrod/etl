<?php

/*
 * This file is part of the AntiMattr ETL, a library by Matthew Fitzgerald.
 *
 * (c) 2014 Matthew Fitzgerald
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AntiMattr\ETL\Load\PDO;

class PDOStatement extends \PDOStatement
{
    protected $debugValues = [];

    protected function __construct()
    {
       // Reset parent::__construct
    }

    /**
     * @param array| null $values
     *
     * @return boolean
     */
    public function execute($values = null)
    {
        $this->debugValues = $values;
        try {
            $success = parent::execute($values);
        } catch (PDOException $e) {
            throw $e;
        }

        return $success;
    }

    /**
     * @param boolean $replaced
     *
     * @return string
     */
    public function debugQuery($replaced = true)
    {
        $q = $this->queryString;

        if (!$replaced) {
            return $q;
        }

        return preg_replace_callback('/:([0-9a-z_]+)/i', array($this, 'debugReplace'), $q);
    }

    /**
     * @param array $matched
     *
     * @return string
     */
    protected function debugReplace(array $matched = [])
    {
        $value = $this->debugValues[$matched[1]];
        if ($value === null) {
            return "NULL";
        }
        if (!is_numeric($value)) {
            $value = str_replace("'", "''", $value);
        }

        return "'". $value ."'";
    }
}
