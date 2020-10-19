<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\RemoteRequestInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Request, Response};
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\{
    ClientExceptionInterface,
    RedirectionExceptionInterface,
    ServerExceptionInterface,
    TransportExceptionInterface
};

/**
 * @Route(path="/api/remote", name="rpc_call")
 */
class RemoteRequestController extends AbstractController
{
    private RemoteRequestInterface $remoteRequest;

    public function __construct(RemoteRequestInterface $remoteRequest)
    {
        $this->remoteRequest = $remoteRequest;
    }

    public function __invoke(Request $request): Response
    {
        try {
            $result = $this->remoteRequest->request($request->getMethod(), 'headers', [
                'headers' => ['Accept' => 'application/ld+json'],
            ]);
        } catch (TransportExceptionInterface $e) {
            return new JsonResponse(['error' => \get_class($e)], 500);
        } catch (RedirectionExceptionInterface | ServerExceptionInterface | ClientExceptionInterface $e) {
            return new JsonResponse(['error' => $e->getMessage()], (int) $e->getResponse()->getStatusCode());
        }

        return new JsonResponse($result, 200, [], true);
    }
}
