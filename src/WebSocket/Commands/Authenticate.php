<?php
declare(strict_types = 1);

namespace Apex\Mercury\WebSocket\Commands;

use Apex\Mercury\WebSocket\Server\{ConnectionManager, ClientConnection};
use Apex\Armor\Armor;
use Apex\Armor\Auth\SessionManager;
use Apex\Container\Di;

/**
 * Authenticate
 */
class Authenticate
{

    /**
     * Process
     */
    public function process(ConnectionManager $manager, ClientConnection $client, array $vars):void
    {

        // Check for admin
        $token = $vars['token'] ?? '';
        if (preg_match("/^admin:(.+)$/", $token, $match)) { 
            if ($match[1] == $manager->getAdminPass()) { 
                $client->setIsAdmin(true);
                $manager->addLog("[ " . $client->getId() . "] Authenticated as admin");
            } else {
                $manager->error("Invalid auth token", $client);
            }
            return;
        }

        // Check if Armor installed
        if (!$armor = $manager->getArmor()) { 
            $manager->error("Armor is not installed, hence authentication does not work.", $client);
            return;
        }

        // Try to get session
        $smanager = Di::make(SessionManager::class);
        if (!$session = $smanager->get($token, false)) { 
            $manager->error("Invalid auth token", $client);
            return;
        }

        // Set auth user
        $manager->setAuthUser($client, $session->getUser());

        // Add log
        $manager->addLog("Authentication successful", $client);
    }

}

