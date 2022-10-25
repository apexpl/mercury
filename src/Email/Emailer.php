<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;

use Apex\Debugger\Interfaces\DebuggerInterface;
use Apex\Mercury\Interfaces\EmailerInterface;
use redis;


/**
 * E-mailer
 */
Class Emailer extends SMTPConnections implements EmailerInterface
{

    /**
     * Constructor
     */
    public function __construct(
        array $smtp = [], 
        ?redis $redis = null, 
        ?DebuggerInterface $debugger = null
    ) {

        // Set redis
        $this->redis = $redis;
        $this->debugger = $debugger;

        // Add connection, if needed
        if (count($smtp) > 0) { 
            $this->addServer($smtp);
        }
    }

    /**
     * Send message
     */
    public function send(EmailMessage $msg, bool $is_persistent = false):bool
    {

        // Get connection
        if (!$sock = $this->getConnection()) { 
            $this->sendPhpMail($msg);
            return true;
        }

        // MAIL FROM
        $res = $this->write($sock, "MAIL FROM: <" . $msg->getFromEmail() . ">");
        if (!str_starts_with($res, '250')) { 
            $this->debugger?->add(3, "SMTP host rejected MAIL FROM with response: $res", 'notice');
            return false;
        }

        // RCPT TO
        $res = $this->write($sock, "RCPT TO: <" . $msg->getToEmail() . ">");
        if (!str_starts_with($res, '250')) { 
            $this->debugger?->add(3, "SMTP host rejected RCPT TO with response: $res", 'notice');
            return false;
        }

        // DATA
        $res = $this->write($sock, "DATA");
        if (!str_starts_with($res, '354')) { 
            $this->debugger?->add(3, "SMTP host rejected DATA command.  Received: $res", 'notice');
            return false;
        }

        // Format message
        $msg->formatMessage();

        // Message contents
        //fwrite($sock, "To: " . $msg->getToLine() . "\r\n");
        //fwrite($sock, "Subject: " . $msg->getSubject() . "\r\n");
        fwrite($sock, $msg->getHeaders() . "\r\n");
        fwrite($sock, $msg->getMessageBody() . "\r\n.\r\n");

        // Check response
        $res = fread($sock, 1024);
        if (!str_starts_with($res, '250')) { 
            $this->debugger?->add(3, "SMTP rejected message after contents.  Received: $res", 'notice');
            return false;
        }

        // Close connection, if needed
        if ($is_persistent === true) { 
            $this->write($sock, "RSET");
        } else { 
            $this->closeConnections();
        }

        // Return
        return true;
    }

    /**
     * Send e-mail via PHP
     */
    public function sendPhpMail(EmailMessage $msg)
    {
        $msg->formatMessage();
        mail($msg->getToLine(), $msg->getSubject(), $msg->getMessageBody(), $msg->getHeaders());
    }

    /**
     * Quick send
     */
    public function quickSend(string $to_email, string $to_name, string $from_email, string $from_name, string $subject, string $message, string $content_type = 'text/plain', array $attachments = [])
    {

        // Define e-mail message
        $email = new EmailMessage(
            to_email: $to_email, 
            to_name: $to_name, 
            from_email: $from_email, 
            from_name: $from_name, 
            content_type: $content_type, 
            subject: $subject, 
            message: $message
        );

        // Add attachments
        foreach ($attachments as $filename => $contents) { 
            $email->addAttachment($filename, $contents);
        }

        // Send e-mail
        $this->send($email);
    }

}



