<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Server;

use Apex\Armor\Auth\AuthSession;
use Ratchet\ConnectionInterface;

/**
 * Client connection
 */
class ClientConnection
{

    // Properties
    public array $channels = [];

    /**
     * Constructor
     */
    public function __construct(
        private string $id, 
        private ConnectionInterface $conn, 
        private string $uuid = '', 
        private string $username = '', 
        private string $user_group = 'public', 
        private bool $is_admin = false
    ) {

    }

    /**
     * Get id
     */
    public function getId():string
    {
        return $this->id;
    }

    /**
     * Get connection
     */
    public function getConn():ConnectionInterface
    {
        return $this->conn;
    }

    /**
     * Get is admin
     */
    public function isAdmin():bool
    {
        return $this->is_admin;
    }

    /**
     * Get uuid
     */
    public function getUuid():string
    {
        return $this->uuid;
    }

    /**
     * Get username
     */
    public function getUsername():string
    {
        return $this->username;
    }

    /**
     * Get user group
     */
    public function getUserGroup():string
    {
        return $this->user_group;
    }

    /**
     * Set is admin
     */
    public function setIsAdmin(bool $val):void
    {
        $this->is_admin = $val;
    }

    /**
     * Set uuid
     */
    public function setUuid(string $uuid):void
    {
        $this->uuid = $uuid;
    }

    /**
     * Set username
     */
    public function setUsername(string $user):void
    {
        $this->username = $user;
    }

    /**
     * Set user group
     */
    public function setUserGroup(string $group):void
    {
        $this->user_group = $group;
    }

    /**
     * Send
     */
    public function send(array $payload = [], string $message = '', string $status = 'ok'):void
    {

        // Set response
        $response = [
            'status' => $status, 
            'version' => '1.0', 
            'message' => $message, 
            'data' => $payload
        ];

        // SEnd to client
        $this->conn->send(json_encode($response));
    }

}


