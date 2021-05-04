
# SMS Messages

Mercury utilizes the [Vongate / Nexmo](https://vonage.com/) API to send SMS messages, meaning you need an account with them to send SMS messages from Mercury.  


## NexmoConfig

To send SMS messages, you first need to create a `Apex\Mercury\SMS\NexmoConfig` object with your Nexmo API account details, and this constructor accepts the following parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$api_key` | Yes | string | Nexmo API key
`$api_secret` | Yes | string | Nexmo API secret
`$sender` | Yes | string | Sender phone number, retrived from Nexmo.

All three variables are required, and retrived from your Nexmo API account.


## Sending SMS Messages

You can send a SMS message by passing the phone number and message to the `Apex\Mercury\SMS\Nexmo::send()` method which only accepts those two arguments.  The `Nexmo` class must be instantiated with a `NexmoConfig` object, for example:

~~~php
use Apex\Mercury\SMS\{Nexmo, NexmoConfig};

// Set config
$config = new NexmoConfig(
    api_key: 'nexmo_api_key', 
    api_secret: 'nexmo_api_secret', 
    sender: '+15551234567'
);

// Send SMS message
$nexmo = new Nexmo($config);
$message_id = $nexmo->send('+14165551234', 'This is a test message');

echo "Message ID: $message_id\n";
~~~

Simple as that, the phone number will be balidated and sent through your Nexmo account.  If any errors occur, a `MercuryNexmoSendException` will be thrown.


## Validating Phone Numbers  

You may also validate a phone number by passing it to the `Apex\Mercury\SMS\Nexmo::validatePhone()` method.  This will either return a string of the phone number in case any refomatting occured, or null on failure.  For example:

~~~php
use Apex\Mercury\SMS\{Nexmo, NexmoConfig};

// Validate phone number
$nexmo = new Nexmo($config);
if (!$phone = $nexmo->validate('1 416-555-1234')) { 
    echo "Phone number is invalid\n";
}
~~~



~~~




