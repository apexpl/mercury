
# RedisManager for SMTP Servers

You may store all SMTP server information within redis which allows for automated rotating of multiple SMTP servers, and centralized storage of SMTP information acorss multiple servers.  This is done through the `Apex\Mercury\Email\RedisManager` class, which accepts the following constructor parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$redis` | Yes | redis | A redis connection.


This class provides the following methods:

* void addServer(array $smtp, string $alias)
* void deleteServer(string $alias)
* array listServers()

When adding a new server, you must supply an `$smtp` array which contains the SMTP server information to add.  This array contains the following keys:

* is_ssl -  A 1 / 0 and whether or not to connect over SSL/TLS.
* host
* port
* user
* password



## Example

~~php
use Apex\Mercury\Email\RedisManager;

// Connect to redis
$redis = new redis();
$redis->connect('127.0.0.1', 6379);

// Define SMTP server
$smtp = [
    'is_ssl' => 1, 
    'host' => 'smtp.sendgrid.net', 
    'port' => 465, 
    'user' => 'apikey', 
    'password' => 'sendgrid_api_key'
];

// Add server
$manager = new RedisManager($redis);
$manager->addServer($smtp, 'sendgrid');

// List servers
$servers = $manager->listServers();
print_r($servers);   // prints array one element -- smtp.sendgrid.net:465

// Delete server
$manager->deleteServer('sendgrid');
~~~

## Using Emailer With redis

After one or more SMTP servers have been added into redis, you no longer need to pass the SMTP information when instantiating the `Emailer` class.  Instead, you may now simply pass the redis connection as the second constructor parameter, for example:

~~~php
use Apex\Mercury\Email\Emailer;

// Connect ro redis
$redis = new redis();
$redis->connect('127.0.0.1', 6379);

// Send e-mail
$emailer = new Emailer([], $redis);
$emailer->quickSend('jsmith@domain.com', 'John Smith', 'me@company.com', 'Company XYZ', 'Test Subject', 'This is a test message');
~~~

That's it, and with the redis connection being passed during instantiation of the `Emiler` class, it will automatically pull the SMTP server from redis as necessary.  By default, it will also automatically rotate all SMTP servers within redis in round robin fashion.


