<?php

namespace App\Exceptions;

use League\Route\Http\Exception\NotFoundException;

class EmailNotSendedException extends NotFoundException
{
    public function __construct($message = "Erro ao enviar email.")
    {
        parent::__construct($message);
    }
}
