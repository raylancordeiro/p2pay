<?php

declare(strict_types=1);

namespace App\Service\Integration;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface as ClientHttpExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * Classe pai das integrações REST.
 */
abstract class BaseRestIntegrationService
{
    protected HttpClientInterface $httpClient;
    protected ?LoggerInterface $logger;

    public function __construct(HttpClientInterface $httpClient, ?LoggerInterface $logger = null)
    {
        $this->httpClient = $httpClient;
        $this->logger     = $logger;
    }

    abstract protected function getBaseUrl(): string;

    abstract protected function getHeaders(): array;

    /**
     * @throws \Throwable
     */
    public function get(string $path, array $options = []): ResponseInterface|array
    {
        return $this->request('GET', $path, $options);
    }

    /**
     * @throws \Throwable
     */
    public function post(string $path, array $options = []): ResponseInterface|array
    {
        return $this->request('POST', $path, $options);
    }

    public function put(string $path, array $options = []): ResponseInterface|array
    {
        return $this->request('PUT', $path, $options);
    }

    /**
     * @throws \Throwable
     */
    public function delete(string $path, array $options = []): ResponseInterface|array
    {
        return $this->request('DELETE', $path, $options);
    }

    /**
     * @throws \Throwable
     */
    protected function request(string $method, string $path, array $options = []): ResponseInterface|array
    {
        $url = rtrim($this->getBaseUrl(), '/').'/'.ltrim($path, '/');

        $options['headers'] = array_merge(
            $this->getHeaders(),
            $options['headers'] ?? []
        );

        try {
            return $this->httpClient->request($method, $url, $options);
        } catch (\Throwable $e) {
            return $this->handleErrors($e);
        }
    }

    /**
     * @throws ClientExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ServerExceptionInterface
     * @throws TransportExceptionInterface
     * @throws \Throwable
     */
    protected function handleErrors(\Throwable $e): array
    {
        if ($e instanceof HttpException) {
            throw $e;
        }

        $statusCode = $this->resolveStatusCode($e);
        $message    = 'Erro inesperado ao comunicar com serviço externo.';
        $details    = [];

        if ($e instanceof ClientHttpExceptionInterface) {
            $response = $e->getResponse();
            try {
                $content = $response->getContent(false);
                $data    = json_decode($content, true, 512, \JSON_THROW_ON_ERROR);

                $message = $data['message'] ?? $message;
                $details = $data['details'] ?? $data;
            } catch (\Throwable) {
                $details = ['raw' => $response->getContent(false)];
            }
        }

        if ($this->logger) {
            $this->logger->error('[Integration Error]', [
                'message'   => $message,
                'code'      => $statusCode,
                'exception' => $e::class,
                'trace'     => $e->getTraceAsString(),
            ]);
        }

        return [
            'error'   => true,
            'message' => $message,
            'code'    => $statusCode,
            'details' => $details,
        ];
    }

    private function resolveStatusCode(\Throwable $e): int
    {
        return match (true) {
            $e instanceof ClientExceptionInterface      => Response::HTTP_BAD_REQUEST,
            $e instanceof RedirectionExceptionInterface => Response::HTTP_FOUND,
            $e instanceof ServerExceptionInterface      => Response::HTTP_INTERNAL_SERVER_ERROR,
            $e instanceof TransportExceptionInterface   => Response::HTTP_SERVICE_UNAVAILABLE,
            default                                     => Response::HTTP_CONFLICT,
        };
    }
}
