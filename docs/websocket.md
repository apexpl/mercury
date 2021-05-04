
# Web Sockets

Mercury includes a web socket server, and although a little rudimentary at the moment and will be strengthened in the near future, it works similar to an IRC server.  Multiple channels that can be opened / closed at will, each of which has a different visibility (public, protected or private) and can consist of any relationship (one-to-one, one-to-many, many-to-many).

Built-in support with the [Armor package](https://github.com/apexpl/armor/) is also included, which allows connections to the web socket server to authenticate as an Armor user.

See the below links to details on how to start the server and listen for connections, and on the `WsClient` which allows you to pragmatically send JSON messages to the server from PHP instead of a typical Javascript client.

* [Start Web Socket Server](server.md)
* [Send Messages via Javascript](javascript.md)
* [Send Messages via WsClient](client.md)


## Overall Architecture

The below table lists the different classes / objects used, which make up the overall architecture of the server:

Class | Description
------------- |------------- 
[ConnectionManager](connection_manager.md) | The web socket server itself, and is passed as an argument to each `process(0` method when performing actions received to the server.
[Channel](channel.md) | Channels can be open and closed at will, each of which individual client connections can join or leave.  Messages can be relayed to everyone in a channel.
[ClientConnection](client_connection.md) | An individual client connected to the server, and if the [Armor package](https://github.com/apexpl/armor) is installed will be binded to an Armor user upon authentication.
[Commands](commands.md) | Individual commands that are performed, which are sent as JSON objects to the server by client connections.




