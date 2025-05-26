<?php

namespace App\Message;

class NotifyMessage
{
    public function __construct(
        public readonly int $value,
        public readonly string $payeeName,
    ) {
    }
}
