<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Server;

use Apex\Mercury\WebSocket\Server\{ClientConnection, Channel};
use Apex\Armor\Interfaces\ArmorUserInterface;


/**
 * Client commands
 */
class ClientCommands
{

    // Properties
    protected array $connections = [];
    protected array $channels = [];
    protected array $uuids = [];
    protected array $user_groups = ['public' => []];
    protected string $error_message = 'undefined';

    /**
     * Get channel
     */
    public function getChannel(string $name):?Channel
    {

        // Check for channel
        if (!isset($this->channels[$name])) { 
            return null;
        }

        // Return
        return $this->channels[$name];
    }

    /**
     * Create channel
     */
    public function createChannel(string $name = '', string $visibility = 'public', ?ClientConnection $client = null):?Channel
    {

        // Get name
        if ($name == '') { 
            $name = uniqid();
        } elseif (isset($this->channels[$name])) { 
            return null;
        }

        // Create channel
        $this->channels[$name] = new Channel(
            name: $name, 
            visibility: $visibility
        );

        // Add user to channel, if defined
        if ($client !== null) { 
            $this->channels[$name]->join($client);
        }

        // Return
        return $this->channels[$name];
    }

    /**
     * Close channel
     */
    public function closeChannel(string $name):bool
    {

        // Get channel
        if (!$channel = $this->getChannel($name)) { 
            return false;
        }

        // Go through members
        $members = $channel->getMembers();
        foreach ($members as $client_id => $access) { 
            $client = $this->connections[$client_id] ?? null;
            if ($client !== null) { 
                $channel->leave($client);
            }
        }

        // Close channel
        unset($this->channels[$name]);
        return true;
    }

    /**
     * Get UUID
     */
    public function getUuid(string $uuid):?ClientConnection
    {

        // Check for client
        $client_id = $this->uuids[$uuid] ?? '';
        if ($client_id == '' || !isset($this->connections[$client_id])) { 
            return null;
        }

        // Return
        return $this->connections[$client_id];
    }

    /**
     * Relay data to channel members
     */
    public function relayChannel(Channel $channel, array $data):int
    {

        // Send to channel members
        $members = $channel->getMembers();
        foreach ($members as $client_id => $access) { 
            $recip = $this->connections[$client_id] ?? null;
            $recip?->send($data);
        }

        // Return
        return count($members);
    }

    /**
     * Relay data to user group
     */
    public function relayUserGroup(string $group, array $data):int
    {

        // Send to channel members
        $members = $this->user_groups[$group] ?? [];
        foreach ($members as $client_id => $access) { 
            $recip = $this->connections[$client_id] ?? null;
            $recip?->send($data);
        }

        // Return
        return count($members);
    }

    /**
     * Set user after authentication
     */
    public function setAuthUser(ClientConnection $client, ArmorUserInterface $user):void
    {

        // Set variables
        $client_id = $client->getId();
        $uuid = $user->getUuid();
        $type = $user->getType();

        // Add to arrays
        $this->uuids[$uuid] = $client->getId();
        unset($this->user_groups['public'][$client_id]);
        if (!isset($this->user_groups[$type])) { 
            $this->user_groups[$type] = [];
        }
        $this->user_groups[$type][$client_id] = $uuid;

        // Set session
        $client->setUuid($uuid);
        $client->setUserGroup($type);
        $client->setUsername($user->getUsername());
    

}

}

