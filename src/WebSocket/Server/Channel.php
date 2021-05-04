<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Server;


/**
 * Channel
 */
class Channel
{

    // Properties
    private array $members = [];
    private array $invites = [];
    private array $history = [];

    /**
     * Constructor
     */
    public function __construct(
        private string $name, 
        private string $visibility = 'public'
    ) { 

    }

    /**
     * Get members
     */
    public function getMembers():array
    {
        return $this->members;
    }

    /**
     * Join channel
     */
    public function join(ClientConnection $client):bool
    {

        // Initialize
        $uuid = $client->getUuid();
        $client_id = $client->getId();
        $access = $this->visibility == 'protected' ? 'read' : 'write';

        // Add
        if (isset($this->members[$client_id])) { 
            return true;
        } elseif (count($this->members) == 0) { 
            $access = 'admin';
        } elseif ($this->visibility == 'private' && !isset($this->invites[$uuid])) { 
            return false;
        } elseif (isset($this->invites[$uuid])) { 
            $access = $this->invites[$uuid];
        }

        // Join channel
        $this->members[$client_id] = $access;
        $client->channels[$this->name] = $access;

        // Return
        return true;
    }

    /**
     * Leave channel
     */
    public function leave(ConnectionClient $client):bool
    {

        // Initialize
        $client_id = $client->getId();

        // Check if exists in channel
        if (!isset($this->members[$client_id])) { 
            return true;
        }

        // Leave
        unset($this->members[$client_id]);
        unset($client->channels[$this->name]);

        // Return
        return true;
    }

    /**
     * Invite user
     */
    public function invite(string $uuid, string $access = 'write'):void
    {
        $this->invites[$uuid] = $access;
    }

    /**
     * Un-invite
     */
    public function uninvite(string $uuid):void
    {
        unset($this->invites[$uuid]);
    }

    /**
     * Set visibility
     */
    public function setVisibility(string $visibility):void
    {
        $this->visibility = $visibility;
    }

    /**
     * Add message
     */
    public function addMessage(ClientConnection $client, string $message):?array
    {

        // Check if allowed
        $client_id = $client->getId();
        if ($client->isAdmin() === false && !isset($this->members[$client_id])) { 
            return null;
        } elseif ($client->isAdmin() === false && $this->members[$client_id] == 'read') { 
            return null;
        }

        // Add to history
        array_unshift($this->history, [
            'client_id' => $client_id, 
            'uuid' => $client->getUuid(), 
            'username' => $client->getUsername(), 
            'time' => time(), 
            'message' => $message
        ]);

        // Return
        return $this->members;
    }


}

