
# WsClient Class

The `Apex\Mercury\WebSocket\WsClient` class is a client that allows you to send messages to the web socket server from within your PHP application versus from within client web browsers.  The constructor accepts the following parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$admin_pass` | Yes | string | The admin password as defined upon instantiating the `WsServer` class.  This is used to authenticate the requests as from the system administrator.
`$host` | No | string | The host of the server.  Defaults to 127.0.0.1
`$port` | No | int | The port of the server.  Defaults to 4863.


## Send Messages

You can send messages to the web socket server using the `bool send(array $data)` method.  The `$data` array passed is encoded in JSON, and that is the exact message sent to the server.  One JSON action will be prefixed to the `$data` array to authenticate the request as from the system administrator.

For example:

~~~php
use Apex\Mercury\WebSocket\WsClient;

// Set message
$msg = [
    [
        'action' => 'message_user', 
        'uuid' => 'u:255', 
        'message' => 'Hello, this is a test'
    ], [
        'action' => 'send_channel', 
        'channel_name' => 'Some Channel', 
        'message' => 'Greeting from admin'
    ]
];

// Send message
$client = new WsClient(
    admin_pass: 'the_admin_password'
);
$client->send($msg);
~~~



