
## EmailMessage Object

Generally to send e-mail messages you will pass an instance of the `Apex\Mercury\Email\EmailMessage` class, which accepts the following constructor parameters:

Variable | Required | Type | Description
------------- |------------- |------------- |------------- 
`$to_email` | No | string 
`$to_name` | No | strin | 
`$from_email` | No | string | 
`$from_name` | No | string | 
`$reply_to` | No | string | The reply-to e-mail address to use, if applicable.
`$cc` | No | array | Array of e-mail addresses for the Cc: header.
`$bcc` | No | array | Array of e-mail addresses for the Bcc: header.
`$subject` | No | string | Subject of the e-mail message
`$content_type` | No | string | The content type, defaults to 'text/plain'.
`$message` | No | string | The body contents of the e-mail message.


## Set Methods

The following set methods are available within the `EmailMessage` class:

* setToEmail(string $email)
* setToName(string $name)
* setFromEmail(string $email)
* setFromName(string $name)
* setReplyTo(string $email)
* addCcEmail(string $email)
* AddBccEmail(string $email)
* setContentType(string $content_type)
* setSubject(string $subject)
* setMessage(string $message)
* addAttachment(string $filename, string $contents)
* deleteAttachment(string $filename)
* purgeAttachments()
* purgeCc()
* purgeBcc()


## Get Methods

The following get methods are available within the `EmailMessage` class:

* getToEmail():string
* getToName():string
* getFromEmail():string
* getFromName():string
* getToLine():string - Returns value to use within To: header that includes name and e-mail address.
* getFromLine():string - Returns value to use within From: header that includes name and e-mail address.
* getReplyTo():string
* getCc():array
* GetBcc():array
* getContentType():string
* getSubject():string
* getMessage():string


## Additional Methods

The following additional methods are available within the `EmailMessage` class:

* getHeaders():void - Is always eecuted before sending via SMTP, and forms the headers as necessary depending whether or not file attachments are included.
* importFromFile(string $filename) - Imports an e-mail message from a file.
* importFromString(string $contents) - Same as above, excpt imports from a string e-mail message.  See below.


## Importing E-Mail Messages

Through the `importFromFile()` and `importFromString()` methods, you can import an existing e-mail message as an object.  The plain text e-mail messages are the same as an actual e-mail with headers and body separated by a blank line, for example:

~~~
From: Company XYZ <me@comainy.com>
Reply-To: <nobody@company.com>
Subject: Test Message
Content-type: text/plain

And here starts the body contents of the e-mail messgae.

Hope you are doing well.

Best,
Company XYZ
~~~

Upon passing a message such as above into either of the methods, it will be parsed accordingly, and all necessary properties will be assigned.


