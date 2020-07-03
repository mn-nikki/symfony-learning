<?php declare(strict_types=1);

namespace App\Tests;

use App\Service\FakeRemoteRequest;
use App\Service\RemoteRequestInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RemoteRequestTest extends KernelTestCase
{
    private RemoteRequestInterface $remoteRequest;

    protected function setUp(): void
    {
        if (!self::$booted) {
            self::bootKernel();
        }

        /* @noinspection PhpFieldAssignmentTypeMismatchInspection */
        $this->remoteRequest = self::$container->get(RemoteRequestInterface::class);
    }

    public function testIsServiceExists(): void
    {
        $this->assertInstanceOf(RemoteRequestInterface::class, $this->remoteRequest);
    }

    public function testServiceClass(): void
    {
        $this->assertInstanceOf(FakeRemoteRequest::class, $this->remoteRequest);
    }

    public function testServiceResponse(): void
    {
        $this->assertStringContainsString('GET', $this->remoteRequest->request('GET', 'path'));
    }
}
