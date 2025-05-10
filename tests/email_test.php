<?php
declare(strict_types = 1);

use Apex\Mercury\Email\{Emailer, EmailMessage};
use PHPUnit\Framework\TestCase;

/**
 * E-mail test
 */
class email_test extends TestCase
{

    /**
     * Test e-mail
     */
    public function test_email_message()
    {

        // Create e-mail
    $email = new EmailMessage(
            to_email: 'jsmith@domain.com', 
            to_name: 'John Smith', 
            from_email: 'mercury@test.com', 
            from_name: 'Mercury', 
            subject: 'Test Subject', 
            message: 'This is a test'
        );

        // Check
        $this->assertEquals(EmailMessage::class, $email::class);
        $this->assertEquals('John Smith <jsmith@domain.com>', $email->getToLine());
        $this->assertEquals('Mercury <mercury@test.com>', $email->getFromLine());

        // Send email
        $emailer = new Emailer();
        $emailer->send($email);
        $this->assertTrue(true);
    }

    /**
     * Test import
     */
    public function test_import()
    {

        // Import
        $email = new EmailMessage();
        $email->importFromFile(__DIR__ . '/email.txt');

        // Check
        $this->assertEquals('jsmith@domain.com', $email->getFromEmail());
        $this->assertEquals('John Smith', $email->getFromName());
        $this->assertEquals('Import Test', $email->getSubject());
        $this->assertEquals('nobody@domain.com', $email->getReplyTo());
        $this->assertEquals('text/html', $email->getContentType());
    }

}


