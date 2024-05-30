<?php

namespace FpDbTest;

use Exception;
use mysqli;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
    }

    public function buildQuery(string $query, array $args = []): string
    {
        
        return $query;
    }

    public function skip()
    {
        return 'SKIP';
    }
    
    private function formatValue($value, $specifier = null)
    {
        if ($value === $this->skip()){
            return 0;
        }elseif (is_string($value)) {
            return "'" . addslashes($value) . "'";
        } elseif (is_int($value)) {
            return (int) $value;
        } elseif (is_float($value)) {
            return (float) $value;
        } elseif (is_bool($value)) {
            return $value ? 1 : 0;
        } elseif ($value === null) {
            return 'NULL';
        } else {
            throw new Exception('Unsupported value type.');
        }
    }
}
