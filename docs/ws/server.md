
# Web Socket - Server

You may start the web server server and begin listening for connections by calling the `Apex\Mercury\WebSocket\WsServer::listen()` method.  This class constructor accepts the following parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$port` | No | int | The port to listen on.  Defaults to 4863.
`$admin_password` | No | string | The administrator password, used when sending via the `WsClient` class to authenticate as the system administrator.  If undefined, a random 36 character password will be generated which can be retrieved via the `getAdminPass()` method.
`$php_namespaces` | No | array | Additional PHP namespaces to look into for back-end commands.  See the [Running Commands](commands.md) page for details.
`$screen_logging` | No | bool | WHether or not to display log messages to the screen.  Defaults to true.
`$armor` | No | Armor | Only applicable if [Armor package](https://github.com/apexpl/armor) package is installed, and you're utilizing its authentication within the web socket server.
`$debugger` | No | DebuggerInterface | Only applicable if you have the [Apex Debugger](https://github.com/apexpl/debugger) and wish to have debugging messages logged.


### Basic Example

~~~php
use Apex\Mercury\WebSocket\WsServer;

// Listen on port 4863
$server = new WsServer();
$server->listen();
~~~


### Example with Armor

~~~php
use Apex\Mercury\WebSocket\WsServer;
use Apex\Armor\Armor;

// Init Armor
$armor = new Armor();

// Start server on port 4100
$server = new WsServer(
    port: 4100, 
    admin_pass: 'my_password', 
    armor: $armor
);

// Listen
$server->listen();
~~~




