
# Web Socket - Javascript Code

You may connect to the web socket server via Javascript using the standard `WebSocket` class, such as:

~~~
<script type="text/javascript">

    var wsConn = new WebSocket('ws://127.0.0.1:4863');
    wsConn.onopen = function(e) {
        wsConn.send('[{"action":"authenticate","token":"' + getCookie('armor_sid_user') + '"}]');
    }

    wsConn.onmessage = function(e) {
        var json = JSON.parse(e.data);

        // Check for error
        if (json.status && json.status == 'error') { 
            alert("WebSocket Error: " + json.message);
            return;
        }

    }
</script>
~~~

The one `wsConn.send()` line is only applicable if you have the [Armor package](https://github.com/apexpl/armor) and wish to use it's user management and authentication functionality to authenticate the user to a web socket server.

## Receiving Messages

All messages received via the `onMessage()` Javascript function will be JSON objects.  Every message will be formatted the same way:

~~~
{
    "status": "ok", 
    "message": "Misc status message", 
    "data": [
        ... JSON objects of actions ...
    ]
}
~~~

A few notes regarding the above JSON message:

* The `status` will always be either "ok" or "error".
* The `message` element is an optional internal message from the server, and not intended for the end-user.
* The `data` element will be a one-dimensiona array of JSON objects, each of which is an assoctivate array of one action to perform within the web browser.


## Sending JSON Messages

All messages sent to the web socket server must be JSON objects comprising of one-dimensional arrays, each element being an associative array of one action to perform.  For example:

~~~
wsConn.send('[{"action":"authenticate","token":"' + getCookie('armor_sid_user') + '"},{"action":"join_channel","channel_name":"example","visibility":"public"}]');
~~~

The above JSOn message sends two actions to the server, one to authenticate via Armor, and another to join a channel.  For details on all commands (ie. "action" values) supported, please visit the [Available Commands](commands.md) page.




