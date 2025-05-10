
# ClientConnection Class

For every individual client who connects to the server, a new instance of the `Apex\Mercury\WebSocket\Server\ClientConnection` class will be created to hold and maintain that connection, which is passed to the `process()` method when performing individual commands.  This class contains the following methods:

* `string getId()`
* `ConnectionInterface getConn()` - Gets the underlying connection.
* `bool isAdmin()` - Whether or not client connection is authenticated as admin.
* `string getUuid()` - If authenticated via Armor, the UUID of the user.
* `string getUsername()` - If authenticated via Armor, the username of the client.
* `string getUserGroup()` - If authenticated via Armor, the user type of the client.
* `void setIsAdmin(bool $is_admin)` - Set whether or not the connection is administrator.
* `void setUuid(string $uuid)`
* `void setUsername(string $username)`
* `void setUserGroup(string $group)`
* `void send(array $payload, string $message = '', string $status = 'ok')` - Sends message to the client's web browser.

When a message is sent to the client's web browser via the `send()` method, the resulting JSON that is sent is formatted as:

~~~
{
    "status": "ok", 
    "message": "Internal message for developers, not intended for end-users", 
    "data": $payload
}
~~~






