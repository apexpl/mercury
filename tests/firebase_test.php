<?php
declare(strict_types = 1);

use Apex\Mercury\Firebase\{Firebase, FirebaseConfig};
use PHPUnit\Framework\TestCase;

/**
 * Firebase test
 */
class firebase_test extends TestCase
{

    /**
     * Test e-mail
     */
    public function test_send()
    {

        // Config
        $config = new FirebaseConfig(
            project_id: 'test', 
            server_key: 'test', 
            sender_id: 'test'
        );

        // Send
        $client = new Firebase($config);
        $num = $client->send('test message', ['test']);
        $this->assertEquals(0, $num);
    }

}


