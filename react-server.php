<?php

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use App\Kernel;

require __DIR__ . '/vendor/autoload.php';

(new Dotenv())->bootEnv(__DIR__ . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$kernel->boot();

$loop = React\EventLoop\Loop::get();

$server = new React\Http\HttpServer(function (Psr\Http\Message\ServerRequestInterface $psrRequest) use ($kernel) {
    try {
        $httpFoundationFactory = new HttpFoundationFactory();
        $symfonyRequest = $httpFoundationFactory->createRequest($psrRequest);

        $symfonyResponse = $kernel->handle($symfonyRequest, HttpKernelInterface::MAIN_REQUEST);

        return new React\Http\Message\Response(
            $symfonyResponse->getStatusCode(),
            $symfonyResponse->headers->all(),
            $symfonyResponse->getContent()
        );
    } catch (\Throwable $e) {
        // Exibe erro no terminal
        echo "Erro: {$e->getMessage()}\n";

        return new React\Http\Message\Response(
            500,
            ['Content-Type' => 'text/plain'],
            "Erro interno: {$e->getMessage()}"
        );
    }
});

$socket = new React\Socket\SocketServer('0.0.0.0:8081');
$server->listen($socket);

echo "✅ ReactPHP + Symfony rodando em http://0.0.0.0:8081\n";

$loop->run();
