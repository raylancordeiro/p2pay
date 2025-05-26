<?php

declare(strict_types=1);

namespace App\Service\Integration;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * Classe responsável pela integração com a API de envio de notificações SMS e e-mail.
 */
class NotifyIntegrationService extends BaseRestIntegrationService
{
    private string $baseUrl;

    public function __construct(
        HttpClientInterface $httpClient,
        ?LoggerInterface $logger,
        string $baseUrl,
    ) {
        parent::__construct($httpClient, $logger);
        $this->baseUrl = $baseUrl;
    }

    protected function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @return string[]
     */
    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    /**
     * @return string[]
     *
     * @throws TransportExceptionInterface
     */
    public function send(int $value, string $payeeName): array
    {
        $response = $this->post('/notify', [
            'json' => [
                'data'=> [
                    'message'=> "Você transferiu R$ {$value} para {$payeeName}",
                ],
            ],
        ]);

        $statusCode = $response->getStatusCode();

        if ($statusCode >= 400) {
            throw new HttpException($statusCode, 'Falha ao enviar notificação.');
        }

        return ['message' => 'Notificação enviada com sucesso.'];
    }
}
