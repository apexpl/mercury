<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;

use Apex\Mercury\Exceptions\MercuryInvalidEmailException;

/**
 * E-mail message model
 */
class EmailMessage
{

    // Properties
    private array $attachments = [];
    private string $headers = '';


    /**
     * Constructor
     */
    public function __construct(
        private string $to_email = '', 
        private string $to_name = '', 
        private string $from_email = '', 
        private string $from_name = '', 
        private string $reply_to = '', 
        private array $cc = [], 
        private array $bcc = [], 
        private string $subject = '', 
        private string $content_type = 'text/plain', 
        private string $message = ''
    ) { 

    }

    /**
     * Set to e-mail
     */
    public function setToEmail(string $email):void
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            throw new MercuryInvalidEmailException("Invalid e-mail address, $email");
        }
        $this->to_email = $email;
    }

    /**
     * Set to name
     */
    public function setToName(string $name):void
    {
        $this->to_name = $name;
    }

    /**
     * Set from
     */
    public function setFromEmail(string $email):void
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            throw new MercuryInvalidEmailException("Invalid e-mail address, $email");
        }
        $this->from_email = $email;
    }

    /**
     * Set from name
     */
    public function setFromName(string $name):void
    {
        $this->from_name = $name;
    }

    /**
     * Set reply-to
     */
    public function setReplyTo(string $email):void
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            throw new MercuryInvalidEmailException("Invalid e-mail address, $email");
        }
        $this->reply_to = $email;
    }

    /**
     * Add CC
     */
    public function addCcEmail(string $email):void
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            throw new MercuryInvalidEmailException("Invalid e-mail address, $email");
        }
        $this->cc[] = $email;
    }

    /**
     * Add BCC
     */
    public function addBccEmail(string $email):void
    {

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { 
            throw new MercuryInvalidEmailException("Invalid e-mail address, $email");
        }
        $this->bcc[] = $email;
    }

    /**
     * Set content type
     */
    public function setContentType(string $content_type):void
    {
        $this->content_type = $content_type;
    }

    /**
     * Set subject
     */
    public function setSubject(string $subject):void
    {
        $this->subject = $subject;
    }

    /**
     * Set message contents
     */
    public function setMessage(string $message):void
    {
        $this->message = $message;
    }

    /**
     * Add attachment
     */
    public function addAttachment(string $filename, string $contents):void
    {
        $this->attachments[$filename] = $contents;
    }

    /**
     * Delete attachment
     */
    public function deleteAttachment(string $filename):void
    {
        unset($this->attachments[$filename]);
    }

    /**
     * Purge attachments
     */
    public function purgeAttachments():void
    {
        $this->attachments = [];
    }

    /**
     * Pruge CC
     */
    public function purgeCc():void
    {
        $this->cc = [];
    }

    /**
     * Purge bcc
     */
    public function purgeBcc():void
    {
        $this->bcc = [];
    }

    /**
 * Get to e-mail
     */
    public function getToEmail():string
    {
        return $this->to_email;
    }

    /**
     * Get to name
     */
    public function getToName():string
    {
        return $this->to_name;
    }

    /**
     * Get from e-mail
     */
    public function getFromEmail():string
    {
        return $this->from_email;
    }

    /**
     * Get from name
     */
    public function getFromName():string
    {
        return $this->from_name;
    }

    /**
     * Get to line
     */
    public function getToLine():string
    {
        $line = '<' . $this->to_email . '>';
        if ($this->to_name != '') { 
            $line = $this->to_name . ' ' . $line;
        }
        return $line;
    }

    /**
     * Get from line
     */
    public function getFromLine():string
    {
        $line = '<' . $this->from_email . '>';
        if ($this->from_name != '') { 
            $line = $this->from_name . ' ' . $line;
        }
        return $line;
    }

    /**
     * get reply to
     */
    public function getReplyTo():string
    {
        return $this->reply_to;
    }

    /**
     * Get CC
     */
    public function getCc():array
    {
        return $this->cc;
    }

    /**
     * Get bcc
     */
    public function getBcc():array
    {
        return $this->bcc;
    }

    /**
     * Get attachment
     */
    public function getAttachments():array
    {
        return $this->attachments;
    }

    /**
     * Get content-type
     */
    public function getContentType():string
    {
        return $this->content_type;
    }

    /**
     * Get subject
     */
    public function getSubject():string
    {
        return $this->subject;
    }

    /**
     * Return message
     */
    public function getMessage():string
    {
        return $this->message;
    }

    /**
     * Get headers
     */
    public function getHeaders():string
    {
        return $this->headers;
    }

    /**
     * Format message
     */
    public function formatMessage():void
    {

        // Start headers
        $headers = "To: " . $this->getToLine() . "\r\n";
        $headers .= "From: " . $this->getFromLine() . "\r\n";

        // Add reply to
        if ($this->reply_to != '') { 
            $headers .= "Reply-to: <" . $this->reply_to . ">\r\n";
        }

        // Add cc
        if (count($this->cc) > 0) { 
            $headers .= "Cc: <" . implode('>, <', $this->cc) . ">\r\n";
        }

        // Add bcc
        if (count($this->bcc) > 0) { 
            $headers .= "Bcc: <" . implode('>, <', $this->bcc) . ">\r\n";
        }

        // Check no attachments
        if (count($this->attachments) == 0) { 
            $this->headers = $headers . "Content-type: $this->content_type\r\n";
            return;
        }

        // Finish headers
        $boundary = "_----------=" . time() . "100";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"";

        // Start message
        $contents = "This is a multi-part message in MIME format.\r\n";
        $contents .= '--' . $boundary . "\r\n";

        // Add message contents
        $contents .= "Content-type: $this->content_type\r\n";
        $contents .= "Content-transfer-encoding: 7bit\r\n\r\n";
        $contents .= $this->message . "\r\n";
        $contents .= '--' . $boundary;

        // Add attachments
        foreach ($this->attachments as $filename => $file_contents) { 
            $contents .= "\r\n";
            $contents .= "Content-Disposition: attachment; filename=\"$filename\"\r\n";
            $contents .= "Content-Transfer-Encoding: base64\r\n";
            $contents .= "Content-Type: application/octet-stream; name=\"$filename\"\r\n\r\n";
            $contents .= base64_encode($file_contents) . "\r\n\r\n";
            $contents .= '--' . $boundary;
        }

        // Finish message
        $contents .= "--\r\n\r\n";
        $this->message = $contents;
        $this->headers = $headers;
    }

    /**
     * Import from file
     */
    public function importFromFile(string $filename):void
    {

        // Get file contents
        if (!file_exists($filename)) { 
            return;
        }

// Import file
        $contents = file_get_contents($filename);
        $this->importFromString($contents);
    }

    /**
     * Import from string
     */
    public function importFromString(string $contents):void
    {

        // Get file
        list($headers, $body) = explode("\n\n", str_replace("\r", "", $contents), 2);
        $header_lines = explode("\n", $headers);

        // GO through header lines
        foreach ($header_lines as $line) { 

            // Check line
            if (!preg_match("/^([\w-]+):(.+)$/", trim($line), $match)) { 
                continue;
            }
            $key = strtolower(trim($match[1]));
            $value = trim($match[2]);

            // From line
            if ($key == 'from') { 

                if (preg_match("/^(.+?)<(.+?)?>/", $value, $from_match)) { 
                    $this->from_email = trim($from_match[2]);
                    $this->from_name = trim($from_match[1]);
                } else { 
                    $this->from_email = $value;
                }

            // Reply-to
            } elseif ($key == 'reply-to') {
                $this->reply_to = preg_replace("/[<>]/", "", trim($value));
            } elseif ($key == 'reply-to') { 
                $this->reply_to = $value;
            } elseif ($key == 'content-type') { 
                $this->content_type = $value;
            } elseif ($key == 'subject') { 
                $this->subject = $value;
            }
        }
        $this->message = $body;

    }

}


