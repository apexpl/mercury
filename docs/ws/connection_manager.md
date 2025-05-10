
# ConnectionManager Class

When the server executes an action, it will pass the `ConnectionManager` object to the `process()` method.  See the `/src/WebSocket/Commands` directory for examples.  This object is the web socket server itself, and contains the following methods:

* `?Channel getChannel(string $name)` - Retrive a [Channel Object](channem.md) of the specified name.  Returns null if channel not open.
* `Channel createChannel(string $name, string $visibility = 'public', ClientConnection $cient)` - Opens new channel with specified name, and adds the given client connection to it.
* `void closeChannel($name)` - Closses the specified channel.
* `ClientConnection getUuid(string $uuid)` - Retrieves the cient connection of the specified uuid, or null if they are not connected.
* `int relayChannel(Channel $channel, array $data)` - Sends the `$data` to all members of the given channel.  Returns total number of messages sent.
* `relayUserGroup(string $group, array $data)` - Sends message to all authenticated users of the specified user group.
* `setAuthUser(ClientConnection $client, ArmorUserInterface $user)` - Used during authentication and will bind a client connection to an Armor suer.

When a message is relayed to a channel or user via the `relayChannel()` or `relayUser()` methods, the resulting JSON that is sent to the client's web browers is formatted as:

~~~
{
    "status": "ok", 
    "message": "Internal message for developers, not intended for end-users", 
    "data": $data
}
~~~


