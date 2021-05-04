<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Commands;

use Apex\Mercury\WebSocket\Server\{ConnectionManager, ClientConnection};


/**
 * Leave Channel
 */
class LeaveChannel
{

    /**
     * Process
     */
    public function process(ConnectionManager $manager, ClientConnection $client, array $vars):void
    {

        // Get channel
        $name = $vars['channel_name'] ?? '';
        if (!$channel = $manager->getChannel($name)) { 
            $manager->error("Channel does not exist, $name");
            return;
        }

        // Leave channel
        $channel->leave($client);
    }

}


