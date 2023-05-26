<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Server;

use Apex\Mercury\WebSocket\Server\{ClientConnection, ClientCommands};
use Apex\Debugger\Interfaces\DebuggerInterface;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\String\UnicodeString;
use Apex\Armor\Armor;

/**
 * Connection manager
 */
class ConnectionManager extends ClientCommands implements MessageComponentInterface
{

    /**
     * Constructor
     */
    public function __construct(
        private string $admin_pass, 
        private array $php_namespaces = [], 
        private bool $screen_logging = true, 
        private ?Armor $armor = null, 
        private ?DebuggerInterface $debugger = null
    ) { 

        // Format php namespaces
        $this->php_namespaces = array_map(function($nm) { 
            if (!str_ends_with($nm, "\\")) { $nm .= "\\"; }
            return $nm;
        }, $this->php_namespaces);

        // Add default to namespaces
        $this->php_namespaces[] = "Apex\\Mercury\\WebSocket\\Commands\\";
    }

    /**
     * Get admin pass
     */
    public function getAdminPass():string
    {
        return $this->admin_pass;
    }

    /**
     * Get armor
     */
    public function getArmor():?Armor
    {
        return $this->armor;
    }

    /**
     * Open connection
     */
    public function onOpen(ConnectionInterface $conn)
    {

        // Get new client connection
        $conn->id = uniqid();
        $client = new ClientConnection(
            id: $conn->id, 
            conn: $conn
        );
        $this->connections[$conn->id] = $client;
        $this->user_groups['public'][$conn->id] = 'public';

        // Add log
        $this->addLog("New Connection", $client);
    }

    /**
     * Receive message
     */
    public function onMessage(ConnectionInterface $conn, $msg)
    {

        // Get client connection
        if (!isset($this->connections[$conn->id])) { 
            $this->addLog("No client connection with id: " . $conn->id, null, 'warning');
            return;
        }
        $client = $this->connections[$conn->id];

        // Decode json
        if (!$json = json_decode($msg, true)) { 
            $this->error("Did not receive valid JSON", $client);
            return;
        }

        // Go through JSON actions
        foreach ($json as $vars) { 
            if (!is_array($vars)) { 
                continue;
            }

            // Process action
            if (!$this->processCommand($client, $vars)) { 
                $this->error("Invalid command, unable to process.  Check the 'action' variable is correct.");
            }
        }

    }

    /**
     * Process command
     */
    private function processCommand(ClientConnection $client, array $vars):bool
    {

        // Get action
        $action = $vars['action'] ?? '';
        if ($action == '') { 
            return false;
        }

        // Convert action to titlecase
        $word = new UnicodeString($action);
        $action = $word->camel()->title();

        // Go through namespaces
        $found=false;
        foreach ($this->php_namespaces as$nm) { 

            // Check if class exists
            $class_name = $nm . $action;
            if (!class_exists($class_name)) { 
                continue;
            }
            $found = true;

            // Load class
            $obj = new $class_name();
            $obj->process($this, $client, $vars);
        }

        // Return
        return $found;
    }

    /**
     * Close connection
     */
    public function onClose(ConnectionInterface $conn) 
    {
        $this->unbindClient($conn);
        unset($this->connections[$conn->id]);
    }

    /**
     * Error
     */
    public function onError(ConnectionInterface $conn, \Exception $e) 
    {
        $e->addLog("Error: " . $e->getMessage(), $this->connections[$conn->id]);
        $this->unbindClient($conn);
        $conn->close();
    }

    /**
     * Unbind client from server.   
     */
    private function unbindClient(ConnectionInterface $conn):void
    {

        // Get client
        if (!isset($this->connections[$conn->id])) {
            return;
        } 
        $client = $this->connections[$conn->id];

        // Leave all channels
        foreach ($client->channels as $name) { 
            $channel = $this->channels[$name] ?? null;
            $channel?->leave($client);
        }

        // Remove from uuid
        $uuid = $client->getUuid();
        if ($uuid != '') { 
            unset($this->uuids[$uuid]);
        }

    // Remove from user groups
        $type = $client->getUserGroup();
        if (isset($this->user_groups[$type])) { 
            unset($this->user_groups[$type][$conn->id]);
        }

    }

    /**
     * Add log
     */
    public function addLog(string $message, ?ClientConnection $client = null, string $level = 'info'):void
    {

        $uuid = $client?->getUuid() ?? 'unknown';
        $line = '[' . $uuid . '] ' . $message;
        if ($this->screen_logging === true) { 
            fputs(STDOUT, "$line\r\n");
        }

        // Add debugger
        $this->debugger?->add(3, "WebSocket: $message", $level);
    }

    /**
     * Error
     */
    public function error(string $message, ClientConnection $client):void
    {
        $this->addLog("ERROR: $message", $client);
        $client->send([], $message, 'error');
    }


}

