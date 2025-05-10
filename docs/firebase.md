
# Firebase Messages

For mobile apps, you can send Firebase messages via Google's API using the `Apex\Mercury\Firebase\Firebase` class.  You must already have a Google account setup with this ability.


## FirebaseConfig

You must create an instance of the `Apex\Mercury\Firebase\FirebaseConfig` class with your API details to send Firebase messages.  The constructor accepts the following parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$project_id` | Yes | string | The project ID.
`$server_key` | Yes | string | The server key.
`$sender_id` | Yes | string | The sender id.
`$app_name` | No | string | The app name.
`$firebase_url` | No | string | The API URL to send to.  Defaults to https://fcm.googleapis.com/fcm/send

All above parameters will be obtained from your Google account.


## Sending Firebase Messages

You may send Firebase messages by calling the `Apex\Mercury\Firebase\Firebase::send()` method, after instantiating the class with passing a `FirebaseConfig` object to the constructor.  The `send()` method accepts the following parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$message` | Yes | string | The message / payload to send, generally a JSON object.
`$android_ids` | No | array | An array of Android device IDs to send to.
`$ios_ids` | No | array | An array of iOS device IDs to send to.
`$type` | No | string | 
`$title` | No | string | 
`$icon` | No | string | 
`$action_id` | No | string | 

This will return an integer with the total number of messages successfully sent.  For example:


~~~php
use Apex\Mercury\Firebase\{Firebase, FirebaseConfig};

// Get config
$config = new FirebaseConfig(
    project_id: 'project_id', 
    server_key: 'server_key', 
    sender_id: 'sender_id'
);

// Send Firebase message
$firebase = new Firebase($config);
$count = $firebase->send("Message to send", ['android-device-id', 'android2']);

echo "Sent total: $total\n";
~~~





