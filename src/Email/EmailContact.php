<?php
declare(strict_types = 1);

namespace Apex\Mercury\Email;


/**
 * E-Mail contact
 */
class EmailContact
{

    /**
     * Constructor
     */
    public function __construct(
        private string $email,
        private ?string $name = null
    ) { 

    }

    /**
     * Get e-mail
     */
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * Get name
     */
    public function getName():?string
    {
        return $this->name;
    }

}

