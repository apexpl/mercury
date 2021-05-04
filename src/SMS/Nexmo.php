<?php
declare(strict_types = 1);

namespace Apex\Mercury\SMS;

use Apex\Mercury\Exceptions\{MercuryNexmoSendException, MercuryInvalidPhoneException};
use Brick\PhoneNumber\PhoneNumber;
use Brick\PhoneNumber\PhoneNumberParseException;

/**
 * Send SMS messages via Nexmo
 */
class Nexmo
{

    /**
     * Construct
     */
    public function __construct(
        private NexmoConfig $config
    ) { 

    }

    /**
     * Send SMS
     */
    public function send(string $phone, string $message):string
    {

        // Validate phone
        if (!$to = $this->validatePhone($phone)) { 
            throw new MercuryInvalidPhoneException("Invalid phone number, $phone");
        }

        // Set request
        $request = [
            'api_key' => $this->config->getApiKey(), 
            'api_secret' => $this->config->getApiSecret(), 
            'from' => $this->config->getSender(), 
            'to' => $to, 
            'text' => $message, 
            'type' => 'text'
        ];
        $request = http_build_query($request);

        // Send request
        $vars = $this->sendHttpRequest($request);
        $msg = $vars['messages'][0];

        // Check message status
        $status = $msg['status'] ?? -1;
        if ((int) $status !== 0) { 
            throw new MercuryNexmoSendException("Invalid status received from Nexmo, $status.  Message: " . $msg['error-text']);
        }

        // Return
        $message_id = $msg['message-id'] ?? 'unknown';
        return $message_id;
    }

    /**
     * Send request via curl
     */
    private function sendHttpRequest(string $request):array
    {

        // Send message via curl
        $ch = curl_init('https://rest.nexmo.com/sms/json');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

        // Send http request
        $response = curl_exec($ch);

        curl_close($ch);

        // Decode json
        if (!$vars = json_decode($response, true)) { 
            throw new MercuryNexmoSendException("Did not receive a valid JSON response from Nexmo.  Response:  $response");
        } elseif (!isset($vars['messages'])) { 
            throw new MercuryNexmoSendException("Received invalid JSON response from Nexmo.  No 'messages' element found.  Response: $response");
        }

        // Return
        return $vars;
    }

    /**
     * Validate phone number
     */
    public function validatePhone(string $phone):?string
    {

        // Initial
        $phone = preg_replace("/[\s\W]/", "", $phone);
        $phone = '+' . $phone;

        // Validate
        // Validate phone
        try {
            $number = PhoneNumber::parse('+' . $phone);
        } catch(PhoneNumberParseException $e) { 
            return null;
        }

        // Return
        return $phone;
    }

}


