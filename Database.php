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
        $pattern = '/\?([dfa#]?)/';
        $query = preg_replace_callback($pattern, function($matches) use (&$args) {
            $arg = array_shift($args);
            if ($matches[1] === '#') {
                if ($arg === $this->skip()) {
                    return '';
                }
                if (is_array($arg)) {
                    return implode(', ', array_map(fn($value) => "`$value`", $arg));
                }
                return "`$arg`";
            }
            if (is_array($arg)) {
                if (array_keys($arg) !== range(0, count($arg) - 1)) {
                    return implode(', ', array_map(function($key, $value) {
                        return "`$key` = " . $this->formatValue($value);
                    }, array_keys($arg), $arg));
                }
                return implode(', ', array_map([$this, 'formatValue'], $arg));
            }

            return $this->formatValue($arg, $matches[1]);
        }, $query);

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
