<?php
declare(strict_types = 1);

namespace Apex\Mercury\SMS;


/**
 * Nexmo configuration
 */
class NexmoConfig
{


    /**
     * Construct
     */
    public function __construct(
        private string $api_key, 
        private string $api_secret, 
        private string $sender
    ) {

    }

    /**
     * Get API key
     */
    public function getApiKey():string
    {
        return $this->api_key;
    }

    /**
     * Get API secret
     */
    public function getApiSecret():string
    {
        return $this->api_secret;
    }

    /**
     * Get sender
     */
    public function getSender():string
    {
        return $this->sender;
    }

}




