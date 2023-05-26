<?php
declare(strict_types = 1);

namespace Apex\Mercury\Interfaces;

/**
 * SMS Client Interface
 */
interface SmsClientInterface
{

    /**
     * Send SMS message
     *
     * @return string The unique id# of the SMS message.
     */
    public function send(string $phone, string $message):string;

    /**
     * Validate phone number
     * 
     * @return string The phone number after any necessary formatting.
     */
    public function validatePhone(string $phone):?string;

}



