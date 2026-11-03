<?php

namespace App\Exceptions;

use DomainException;

class RegleMetierException extends DomainException
{
    public function __construct(string $message, public string $champ = 'general')
    {
        parent::__construct($message);
    }
}