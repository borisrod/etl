<?php

namespace AntiMattr\Tests\ETL;

class MockPDOStatement extends \PDOStatement
{
    public function __construct()
    {
        // \PDO can not be mocked directly
    }
}
