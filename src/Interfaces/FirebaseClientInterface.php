<?php
declare(strict_types = 1);

namespace Apex\Mercury\Interfaces;

/**
 * Firebase interface
 */
interface FirebaseClientInterface
{

    /**
     * Send Firebase message
     */
    public function send(
        string $message, 
        array $android_ids = [], 
        array $ios_ids = [], 
        string $type = '', 
        string $title = '', 
        string $icon = '', 
        string $action_id = ''
    ):int;

}



