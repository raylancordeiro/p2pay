<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Usuário não encontrado.')
    {
        parent::__construct($message);
    }
}
