<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Classe responsável pela exceção de usuário não encontrado.
 */
class UserNotFoundException extends NotFoundHttpException
{
    public function __construct(string $message = 'Usuário não encontrado.')
    {
        parent::__construct($message);
    }
}
