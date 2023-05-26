<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Commands;

use Apex\Mercury\WebSocket\Server\{ConnectionManager, ClientConnection};


/**
 * Join Channel
 */
class JoinChannel
{

    /**
     * Process
     */
    public function process(ConnectionManager $manager, ClientConnection $client, array $vars):void
    {

        // Set variables
        $name = $vars['channel_name'] ?? uniqid();
        $visibility = $vars['visibility'] ?? 'public';
        $join_or_create = $vars['join_or_create'] ?? 1;

        // Get channel
        if (!$channel = $manager->getChannel($name)) { 

            // Create channel
            if ($join_or_create == 1) { 
                $channel = $manager->createChannel($name, $visibility, $client);
            } else { 
                $manager->error("Channel does not exist, $name", $client);
            }

        // Join channel
        } elseif (!$channel->join($client)) { 
            $manager->error("Unable to create channel, $name.  Permission denied", $client);
        }

    }

}


