<?php

namespace AntiMattr\Tests\ETL;

class MockPDO extends \PDO
{
    public function __construct()
    {
        // \PDO can not be mocked directly
    }
}
