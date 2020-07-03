<?php declare(strict_types=1);

namespace App\Service;

use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};
use Symfony\Contracts\HttpClient\HttpClientInterface;

class RemoteRequestService implements RemoteRequestInterface
{
    private HttpClientInterface $httpClient;
    private string $baseUrl;

    public function __construct(HttpClientInterface $httpClient, string $baseUrl)
    {
        $this->httpClient = $httpClient;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $method
     * @param string $path
     * @param array  $data
     *
     * @return string
     *
     * @throws ClientExceptionInterface|RedirectionExceptionInterface|ServerExceptionInterface|TransportExceptionInterface
     */
    public function request(string $method, string $path, array $data = []): string
    {
        $url = \sprintf('%s/%s', \rtrim($this->baseUrl, '/'), \ltrim($path, '/'));

        return $this->httpClient->request($method, $url, $data)
            ->getContent();
    }
}
