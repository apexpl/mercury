
# Channel

The `Apex\WebSocket\Server\Channel` class handles the various channels created within the web socket server, and contains the following methods:

* `array getMembers()` - Returns array of all client IDs who are within the channel.
* `bool join(ClientConnection $client)` - Adds a client connection to the channel.
* `void leave(ClientConnection $client)` - Removes a client connection from the channel.
* `bool invite(string $uuid, string $access = 'user')` - Only applicable for private channels, and will invite a user allowing them to join.
* `void uninvite(string $uuid)` - Only applicable for private channels, and removes invitation for user.
* `void setVisibility(string $visibility)` - Sets visibility of channel, can be either: `public, protected, private`.
* `void addMessage(ClientConnection $client, string $message)` - Adds a message to the chat history from the client connection.


**NOTE:** It's understood there is still fairly rudimentary, and an upgrade will be released in the near future.

