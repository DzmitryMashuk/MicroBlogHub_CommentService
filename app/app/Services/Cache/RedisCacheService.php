<?php

declare(strict_types=1);

namespace App\Services\Cache;

use Illuminate\Support\Facades\Redis;

class RedisCacheService
{
    public function get(string $key): ?string
    {
        return Redis::get($key);
    }

    public function set(string $key, string $value, ?int $expireTime = null): bool
    {
        if ($expireTime) {
            return (bool) Redis::setex($key, $expireTime, $value);
        }

        return (bool) Redis::set($key, $value);
    }

    public function delete(string $key): bool
    {
        return (bool) Redis::del($key);
    }
}
