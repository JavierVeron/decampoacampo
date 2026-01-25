<?php

namespace App\Classes;

use Redis;

class CacheManager {
    private static $instance = null;
    private $redis;

    private function __construct() {
        $this->redis = new Redis();
        $this->redis->connect(getenv('REDIS_HOST') ?: 'redis', 6379);
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function remember(string $key, int $ttl, callable $callback) {
        $value = $this->redis->get($key);

        if ($value !== false) {
            return json_decode($value, true);
        }

        $data = $callback();

        $this->redis->setex($key, $ttl, json_encode($data));

        return $data;
    }

    public function delete(string $key) {
        return $this->redis->del($key);
    }

    public function flush() {
        return $this->redis->flushDb(true);
    }
}