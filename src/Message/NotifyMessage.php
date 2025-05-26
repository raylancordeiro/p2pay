<?php

declare(strict_types=1);

namespace App\Message;

/**
 * Classe que define os atributos da mensagem que será consumida pela fila.
 */
class NotifyMessage
{
    public function __construct(
        public readonly int $value,
        public readonly string $payeeName,
    ) {
    }
}
