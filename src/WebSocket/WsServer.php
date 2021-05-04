<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket;

use Apex\Mercury\WebSocket\Server\ConnectionManager;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer as RatchetServer;
use Apex\Armor\Armor;
use Apex\Debugger\Interfaces\DebuggerInterface;

/**
 * Web socket server
 */
class WsServer
{

    /**
     * Constructor
     */
    public function __construct(
        private int $port = 4863, 
        private string $admin_pass = '', 
        private array $php_namespaces = [], 
        private bool $screen_logging = true, 
        private ?Armor $armor = null, 
        private ?DebuggerInterface $debugger = null
    ) {

        // Generate password, if needed
        if ($this->admin_pass == '') {
            $this->admin_pass = unpack("H*", openssl_random_pseudo_bytes(18))[1];
        }

        // Init connection manager
        $this->manager = new ConnectionManager(
            admin_pass: $this->admin_pass
            php_namespaces: $this->php_namespaces, 
            screen_logging: $this->screen_logging, 
            armor: $this->armor, 
            debugger: $this->debugger
        );

        }

    }

    /**
     * Get admin pass
     */
    public function getAdminPass():string
    {
        return $this->admin_pass;
    }

    /**
     * Listen
     */
    public function listen()
    {

        // Initialize server
        $http = new HttpServer(
            new RatchetServer($this->manager)
        );
        $server = IoServer::factory($http, $this->port);

    // Echo banner
    fputs(STDOUT, "Listening for web socket connections on port $this->port ...\n");

        // Run server
        $server->run();
    }

}


