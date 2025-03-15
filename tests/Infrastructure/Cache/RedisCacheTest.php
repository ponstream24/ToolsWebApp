<?php

namespace Tests\Infrastructure\Cache;

use PHPUnit\Framework\TestCase;
use Infrastructure\Cache\RedisCache;
use Predis\Client;
use Mockery;

class RedisCacheTest extends TestCase
{
    private Client $redis;

    protected function setUp(): void
    {
        $this->redis = Mockery::mock(Client::class);
        RedisCache::setInstance($this->redis);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        RedisCache::clearInstance();
    }

    public function testSetValue(): void
    {
        $key = 'test_key';
        $value = 'test_value';
        $ttl = 3600;

        $this->redis->shouldReceive('set')
            ->once()
            ->with($key, $value)
            ->andReturn(true);

        $this->redis->shouldReceive('expire')
            ->once()
            ->with($key, $ttl)
            ->andReturn(true);

        RedisCache::set($key, $value, $ttl);
        $this->assertTrue(true, 'Method executed without throwing exceptions');
    }

    public function testGetValue(): void
    {
        $key = 'test_key';
        $value = 'test_value';

        $this->redis->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn($value);

        $result = RedisCache::get($key);
        $this->assertEquals($value, $result);
    }

    public function testGetNonExistentValue(): void
    {
        $key = 'non_existent_key';

        $this->redis->shouldReceive('get')
            ->once()
            ->with($key)
            ->andReturn(null);

        $result = RedisCache::get($key);
        $this->assertNull($result);
    }

    public function testDeleteValue(): void
    {
        $key = 'test_key';

        $this->redis->shouldReceive('del')
            ->once()
            ->with($key)
            ->andReturn(1);

        RedisCache::delete($key);
        $this->assertTrue(true, 'Method executed without throwing exceptions');
    }

    public function testExists(): void
    {
        $key = 'test_key';

        $this->redis->shouldReceive('exists')
            ->once()
            ->with([$key])
            ->andReturn(1);

        $result = RedisCache::exists($key);
        $this->assertTrue($result);
    }

    public function testNotExists(): void
    {
        $key = 'non_existent_key';

        $this->redis->shouldReceive('exists')
            ->once()
            ->with([$key])
            ->andReturn(0);

        $result = RedisCache::exists($key);
        $this->assertFalse($result);
    }
} 