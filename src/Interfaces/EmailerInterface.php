<?php
declare(strict_types = 1);

namespace Apex\Mercury\Interfaces;

use Apex\Mercury\Email\EmailMessage;

/**
 * E-mailer interface
 */
interface EmailerInterface
{

    /**
     * Send e-mail
     */
    public function send(EmailMessage $msg, bool $is_persistent = false):bool;

}


