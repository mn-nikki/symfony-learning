<?php declare(strict_types=1);

namespace App\Controller;

use App\Repository\ModelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private ModelRepository $modelRep;

    public function __construct(ModelRepository $modelRep)
    {
        $this->modelRep = $modelRep;
    }

    /**
     * @Route(path="/path/models/{page<\d+>?1}")
     *
     * @param int $page
     * @param int $count
     *
     * @return Response
     */
    public function index(int $page = 1, int $count = 10): Response
    {
        $models = $this->modelRep->getModelsWithParams($page, $count);

        return $this->render('path/index.html.twig', [
            'data' => $models,
            'page' => $page,
            'maxPage' => \ceil($models->count() / $count),
        ]);
    }
}
