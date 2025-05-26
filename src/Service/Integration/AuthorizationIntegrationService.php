<?php

namespace App\Service\Integration;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class AuthorizationIntegrationService extends BaseRestIntegrationService
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

    protected function getHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
        ];
    }

    public function authorize(): array
    {
        $response = $this->get('/authorize');

        $data = $response instanceof ResponseInterface
            ? $response->toArray(false)
            : $response;

        if (!isset($data['data']['authorization'])) {
            throw new HttpException(Response::HTTP_SERVICE_UNAVAILABLE, 'Serviço de autorização indisponível.');
        }

        if (false === $data['data']['authorization']) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'A autorização foi negada pela API externa.');
        }

        return $data;
    }
}
