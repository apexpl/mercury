<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Commands;

use Apex\Mercury\WebSocket\Server\{ConnectionManager, ClientConnection};


/**
 * Message user
 */
class MessageUser
{

    /**
     * Process
     */
    public function process(ConnectionManager $manager, ClientConnection $client, array $vars):void
    {

        // Get client
        $uuid = $vars['uuid'] ?? '';
        if (!$recip = $manager->getUuid($uuid)) { 
            $manager->error("The user is not connected with uuid: $uuid", $client);
            return;
        } elseif ($client->getUuid() == '') { 
            $manager->error("You must be authenticated to send private message.", $client);
            return;
        }

        // Get message
        $message = $vars['message'] ?? '';
        if ($message == '') { 
            $manager->error("You can not send a blank message.", $client);
            return;
        }

        // Get channel name
        $uuids = [$uuid, $client->getUuid];
        asort($uuids);
        $name = 'pm:' . implode('+', $uuids);

        // Create channel, if needed
        if (!$channel = $manager->getChannel($name)) { 
            $channel = $manager->createChannel($name, 'public', $client);
            $channel->join($recip);
            $channel->setVisibility('private');
        }

        // Add message to channel
        $channel->addMessage($client, $message);

        // Set response
        $response = [[
            'action' => 'new_message', 
            'channel_name' => $name, 
            'message' => '[' . date('H:i') . "] &lt;" . $client->getUsername() . "&gt;: " . $message
        ]];

        // Send message
        $manager->relayChannel($channel, $response);
    }

}


