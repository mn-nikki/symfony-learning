<?php declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

/**
 * CurrencyController.
 *
 * @Route(name="currency", path="/currency")
 */
class CurrencyController extends AbstractController
{
    private HttpClientInterface $httpClient;
    private CacheInterface $cache;

    public function __construct(HttpClientInterface $httpClient, CacheInterface $cache)
    {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    public function __invoke()
    {
        return new Response($this->getData(), Response::HTTP_OK, [
            'Content-Type' => 'text/xml; charset=windows-1251',
            'encoding' => 'windows-1251',
        ]);
    }

    /**
     * @return string
     *
     * @throws \Psr\Cache\InvalidArgumentException
     */
    private function getData(): string
    {
        $value = $this->cache->get('currency', function (ItemInterface $item) {
            $item->expiresAfter(3600);

            $computed = $this->httpClient->request('GET', 'http://www.cbr.ru/scripts/XML_daily.asp')
                ->getContent();

            return $computed;
        });

        return $value;
    }
}
