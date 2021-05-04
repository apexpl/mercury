<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Commands;

use Apex\Mercury\WebSocket\Server\{ConnectionManager, ClientConnection};


/**
 * Message channel
 */
class MessageChannel
{

    /**
     * Process
     */
    public function process(ConnectionManager $manager, ClientConnection $client, array $vars):void
    {

        // Get channel
        $name = $vars['channel_name'] ?? '';
        if (!$channel = $manager->getChannel($name)) { 
            $manager->error("Channel does not exist, $name", $client);
            return;
        }

        // Get message
        $message = $vars['message'] ?? '';
        if ($message == '') { 
            $manager->error("Can not send a blank message", $client);
        }

        // Add message to channel
        if (!$channel->addMessage($client, $message)) { 
            $manager->error("Unable to send to channel.  Permission denied.");
            return;
        }

        // Set response
        $response = [[
            'action' => 'new_message', 
            'channel_name' => $name, 
            'message' => '[' . date('H:i') . "] &lt;" . $client->getUsername() . "&gt;: " . $message
        ]];

        // Relay message to channel members
        $manager->relayChannel($channel, $response);
    }

}


