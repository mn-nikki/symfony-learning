<?php declare(strict_types=1);


namespace App\Controller\Api;


use App\Service\PizzaManager;
use App\Service\PizzaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/")
 */
class PizzaController extends AbstractController
{
    protected static array $formats = [
        'application/json' => 'json',
        'application/xml' => 'xml',
        'text/csv' => 'csv',
    ];

    private PizzaManagerInterface $pizzaManager;
    private SerializerInterface $serializer;

    public function __construct(PizzaManagerInterface $pizzaManager, SerializerInterface $serializer)
    {
        $this->pizzaManager = $pizzaManager;
        $this->serializer = $serializer;
    }

    /**
     * @Route("index/{page<\d+>}", name="api_index")
     *
     * @param Request $request
     * @param int $page
     * @return Response
     */
    public function index(Request $request, int $page = 1): Response
    {
        $data = $this->pizzaManager->pager(
            $page,
            (int) $request->query->get('pageSize', PizzaManager::DEFAULT_PAGE_SIZE),
            $request->query->get('orderBy', PizzaManager::DEFAULT_ORDER_PROPERTY),
            $request->query->get('order', PizzaManager::DEFAULT_ORDER_DIRECTION),
        );

        $format = $request->headers->get('Accept', 'application/json');
        if (!\array_key_exists($format, self::$formats)) {
            $format = 'application/json';
        }

        $serializedData = $this->serializer->serialize($data, self::$formats[$format], [
            'groups' => 'index',
        ]);

        return new Response($serializedData, Response::HTTP_OK, [
            'Content-Type' => \sprintf('%s; charset=utf-8', $format),
        ]);
    }
}
