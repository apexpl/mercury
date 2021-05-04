<?php
declare(strict_types = 1);

namespace Apex\Mercury\Firebase;


/**
 * Firebase config
 */
class FirebaseConfig
{

    /**
     * Constructor
     */
    public function __construct(
        private string $project_id, 
        private string $server_key, 
        private string $sender_id, 
        private string $app_name = '', 
        private string $firebase_url = 'https://fcm.googleapis.com/fcm/send'
    ) { 

    }

    /**
     * Get project id
     */
    public function getProjectId():string
    {
        return $this->project_id;
    }

    /**
     * Get server key
     */
    public function getServerKey():string
    {
        return $this->server_key;
    }

    /**
     * Get sender id
     */
    public function getSenderId():string
    {
        return $this->sender_id;
    }

    /**
     * Get app name
     */
    public function getAppName():string
    {
        return $this->app_name;
    }

    /**
     * Get URL
     */
    public function getFirebaseUrl():string
    {
        return $this->firebase_url;
    }

}


