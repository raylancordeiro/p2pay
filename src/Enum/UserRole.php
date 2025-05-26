<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * Enum que armazena os tipos dos usuários.
 */
enum UserRole: string
{
    case PERSON     = 'person';
    case SHOPKEEPER = 'shopkeeper';
}
