<?php

declare(strict_types=1);

namespace App\Validator\Constraints;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserExists extends Constraint
{
    public string $message = 'O usuário com ID {{ id }} não existe.';
}
