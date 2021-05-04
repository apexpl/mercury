
# E-Mail Messages

Mercury allows the easy sending of e-mail messages, and also supports multiple rotating SMTP servers with redis management for centralized storage.  All e-mails are sent via the `Apex\Mercury\Email\Emailer` class, which accepts the following parameters within the constructor:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$smtp` | No | array | SMTP server information to use.  See below for array details.
`$redis` | no | redis | A redis connection, if storing SMTP connection within redis for rotating SMTP servers and centralized management.  See the [Redis Manager](email_redis.md) page for details.
`Debugger` | No | DebuggerInterface | Only applicable if you have the [Apex Debugger](https://github.com/apexpl/debugger) package installed, and wish to enable debugging.


The `$smtp` array contains the following elements:

* is_ssl -  A 1 / 0 and whether or not to connect over SSL/TLS.
* host
* port
* user
* password


## Sending E-Mails

You may send e-mail messages by calling the `Apex\Mercury\Email\Emailer::send()` method, and password an `EmailMessage` object to it.  For detauls on the `EmailMessage` object, please see the [EmailMessage Class](email_message.md) page for details.

For example:

~~~php
use Apex\Mercury\Email\{Emailer, EmailMessage};

// Define e-mail message
$msg = new EmailMessage(
    to_email: jsmith@domain.com, 
    to_name: 'John Smith', 
    from_email: 'me@company.com', 
    from_name: 'Company XYZ', 
    content_type: 'text/plain', 
    subject: 'Checking In with Test', 
    message: "This is a test e-mail message"
);

// Get e-mailer
$emailer = new Emailer([
    'is_ssl' => 1, 
    'host' => 'mail.domain.com', 
    'port' => 465, 
    'user' => 'smtp_user', 
    'password' => 'smtp_password'
]);

// Send message
$emailer->send($msg);
~~~

If no SMTP infromation is defined or available, it will default to sending via PHP internal `mail()` function.


## Quick E-Mail Send

Instead of passing an `EmailMessage` object, you may use the `quickSend()` method to quickly send an e-mail message.  This method accepts the following parameters:

Variable | Required | Type \ Description
------------- |------------- |------------- |------------- 
`$to_email` | Yes | string 
`$to_name` | Yes | strin | 
`$from_email` | Yes | strin | 
`$from_name` | Yes | string | 
`$subject` | Yes | string | Subject of the e-mail message
`$message` | Yes | strin | The body contents of the e-mail message.
`$content_type` | No | string | The content type, defaults to 'text/plain'.
`$attachments` | No | array | File attachments to send, keys being the filenmae and value the contents of the file.

For example:

~~~php
use Apex\Mercury\Email\Emailer;

// Init
$emailer = new Emailer([], $redis);
$emailer->quickSend('jsmith@domain.com', 'John Smith', 'me@domain.com', 'Company XYZ', 'Test subject', "This ia a test message');
~~~

That's it, and an e-mail will be dispatched to `jsmith@domain.com`.


