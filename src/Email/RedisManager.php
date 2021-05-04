<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;

use Apex\Mercury\Email\SMTPConnections;
use Apex\Mercury\Exceptions\MercuryInvalidConfigException;
use redis;

/**
 * Redis manager
 */
class RedisManager
{

    /**
     * Constructor
     */
    public function __construct(
        private redis $redis
    ) {

    }

    /**
     * Add server
     */
    public function addServer(array $vars, string $alias = ''):void
    {

        // Get alias
        if ($alias == '') { 
            $alias = uniqid();
        }

        // Validate SMTP info
        $vars = SMTPConnections::validateServer($vars);

        // Check if already in redis
        $aliases = $this->redis->lrange('config:mercury.smtp_servers', 0, -1) ?? [];
        if (in_array($alias, $aliases)) { 
            throw new MercuryInvalidConfigException("An SMTP server with the alias '$alias' already exists within redis.");
        }

        // Add to redis
        $this->redis->hmset('config:mercury.smtp_servers.' . $alias, $vars);
        $this->redis->lpush('config:mercury.smtp_servers', $alias);
    }

    /**
     * Delete
     */
    public function deleteServer(string $alias):void
    {
        $this->redis->lrem('config:mercury.smtp_servers', $alias, 1);
        $this->redis->del('config:mercury.smtp_servers.' . $alias);
    }

    /**
     * List all servers
     */
    public function listServers():array
    {

        // Initialize
        $servers = [];
        $aliases = $this->redis->lrange('config:mercury.smtp_servers', 0, -1); 

        // Go through servers
        foreach ($aliases as $alias) { 

            // Get from redis
            if (!$vars = $this->redis->hgetall('config:mercury.smtp_servers.' . $alias)) { 
                continue;
            }
            $servers[$alias] = $vars['host'] . ':' . $vars['port'];
        }

        // Return
        return $servers;
    }

}



