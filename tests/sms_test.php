<?php
declare(strict_types = 1);

use Apex\Mercury\SMS\{NexmoConfig, Nexmo};
use Apex\Mercury\Exceptions\{MercuryInvalidPhoneException, MercuryNexmoSendException};
use PHPUnit\Framework\TestCase;

/**
 * SMS test
 */
class sms_test extends TestCase
{

    /**
     * Test e-mail
     */
    public function test_sms()
    {

        // Config
        $config = new NexmoConfig(
            api_key: 'test', 
            api_secret: 'test', 
            sender: '+14165129941'
        );

        // Send
        $nexmo = new Nexmo($config);
        $this->expectException(MercuryNexmoSendException::class);
        $nexmo->send('+14165551234', 'Test');
    }

    /**
     * Test validate
     */
    public function test_validate()
    {

        // Config
        $config = new NexmoConfig(
            api_key: 'test', 
            api_secret: 'test', 
            sender: '+14165129941'
        );

        // Validate
        $client = new Nexmo($config);
        $ok = $client->validatePhone('58f125');
        $this->assertNull($ok);

        // Validate again
        $ok = $client->ValidatePhone('+1 (416) 555-1234');
        $this->assertEquals('+14165551234', $ok);
    }




}


