<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};

interface RemoteRequestInterface
{
    /**
     * Sends HTTP request and returns the response.
     *
     * @param string $method
     * @param string $path
     * @param array  $data
     *
     * @return string
     *
     * @throws ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function request(string $method, string $path, array $data = []): string;
}
