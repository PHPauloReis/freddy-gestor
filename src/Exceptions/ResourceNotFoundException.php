<?php

namespace App\Exceptions;

use League\Route\Http\Exception\NotFoundException;

class ResourceNotFoundException extends NotFoundException
{
    public function __construct($message = "Recurso não encontrado.")
    {
        parent::__construct($message);
    }
}
