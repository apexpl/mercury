<?php
declare(strict_types = 1);

use Apex\Mercury\Email\RedisManager;
use PHPUnit\Framework\TestCase;

/**
 * Firebase test
 */
class email_redis_test extends TestCase
{

    /**
     * Test e-mail
     */
    public function test_redis()
    {

        // Set smtp
        $smtp = ['is_ssl' => 1, 'host' => 'mail.domain.com', 'port' => 465, 'user' => 'username', 'password' => 'somepass'];

        // Init
        $redis = new redis();
        $redis->connect('127.0.0.1', 6379);
        $redis->select(15);

        // Clear redis
        $keys = $redis->keys('config:mercury*');
        foreach ($keys as $key) { 
            $redis->del($key);
        }

        // Add
        $manager = new RedisManager($redis);
        $manager->addServer($smtp, 'testsrv');

        // List
        $list = $manager->listServers();
        $this->assertIsArray($list);
        $this->assertCount(1, $list);
        $key = array_keys($list)[0];
        $this->assertEquals('mail.domain.com:465', $list[$key]);

        // Delete
        $manager->deleteServer('testsrv');
        $list = $manager->listServers();
        $this->assertIsArray($list);
        $this->assertCount(0, $list);

        // Clear redis
        $keys = $redis->keys('config:mercury*');
        foreach ($keys as $key) { 
            $redis->del($key);
        }

    }

}

