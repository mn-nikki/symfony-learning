<?php declare(strict_types=1);

namespace App\Service;

class FakeRemoteRequest implements RemoteRequestInterface
{
    /**
     * @inheritDoc
     */
    public function request(string $method, string $path, array $data = []): string
    {
        return \sprintf('Method: %s, path: %s', $method, $path);
    }
}
