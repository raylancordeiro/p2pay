<?php

declare(strict_types=1);

namespace App\MessageHandler;

use App\Message\NotifyMessage;
use App\Service\Integration\NotifyIntegrationService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

/**
 * Classe responsável pela ação que será realizada pela fila.
 */
#[AsMessageHandler]
class NotifyMessageHandler
{
    public function __construct(
        private readonly NotifyIntegrationService $notifyService,
    ) {
    }

    public function __invoke(NotifyMessage $message): void
    {
        $this->notifyService->send($message->value, $message->payeeName);
    }
}
