
# Running Commands

Every message sent to the web socket server must be JSON encoded, and be a one dimensional array of actions to perform.  Each action is an associative array which contains an "action" variable defining the action to perform, plus any additional variables for the action.  For example:

~~~
[{
    "action": "authenticate", 
    "token": "admin:the_admin_password"
}, {
    "action": "send_channel", 
    "channel_name": "some channel name", 
    "message": "Hi from admin"
}]
~~~

The above contains two commands, one to authenticate and another to message everyone in a channel.  

## Available Commands

Although this list will be expanded in the near future, as of this writing the following default commands are supported in installation:

Action | Description
------------- |------------- 
authenticate | Only applicable if Armor is installed, and will authenticate the user.  Only additional variable is "token", which can either be "admin:ADMIN_PASSWORD" or the session id of the user's Armor session which is the value of the "armor_sed_user" cookie.
join_channel | Join a channel.  Additional variables are "channel_name" which can be any alpha-numeric string you wish, "join_or_create" whcih is 1 or 0 and specifies if the channel should be created if it doesn't already exist.
leave_channel | Leave a channel.  Only additional variable is "channel_name".
message_channel | Send a message to everything in a channel.  Additional variables are "channel_name" and "message".
message_user | Send private message to a user.  Additional variables are "uuid" and "message".


## Per-Command PHP Classes

Every action performed by the server is a separate PHP class named the action.  Upon instantiation of the `Apex\Mercury\WebSocket\WsServer` class, the constructor accepts an optional `$php_classes` array, which is a list of all namespaces the server will look for a PHP class that matches the name of the action being performed.  

Aside from any namespaces defined within the `$php_namespaces` array, the `Apex\Mercury\WebSocket\Commands` namespace is also checked.  The server will also automatically convert all actions to titlecase.

For example, if the server receives a JSON object with the "action" of "message_channel", it will look for a `MessageChannel` PHP class within the designated namespaces.  Upon finding a PHP class, it will call the `process()` method within it, which accepts the following parameters:

Variable | Type | Description
------------- |------------- |------------- 
`$manager` | ConnectionManager | The socket server itself, see the [ConnectionManager Class](connection_manager.md) for details and available methods.
`$client` | ClientConnection | The client who is performing the action.  See the [ClientConnection Class\(client_connection.md) page for details.
`$vars` | array | The variables passed within the JSON object.

Please see the `/src/WebSocket/Commands` directory for code examples.



