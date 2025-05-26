<?php

declare(strict_types=1);

namespace App\Controller;

use App\Message\NotifyMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Classe criada apenas para testes.
 */
class TestController extends AbstractController
{
    #[Route('/test', name: 'app_test', methods: ['GET'])]
    public function test(MessageBusInterface $bus): JsonResponse
    {
        $bus->dispatch(new NotifyMessage(
            value: 1234,
            payeeName: 'João da Silva'
        ));

        return new JsonResponse([
            'message' => 'Notificação enviada para a fila com sucesso.',
        ]);
    }
}
