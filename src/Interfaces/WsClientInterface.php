<?php
declare(strict_types = 1);

namespace Apex\Mercury\Interfaces;

/**
 * WS Client Interface
 */
interface WsClientInterface
{

    /**
     * Send message
     */
    public function send(array $data):bool;

}


