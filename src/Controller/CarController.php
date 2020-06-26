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
     * @Route(path="/path/models")
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('path/index.html.twig', [
            'data' => $this->modelRep->getModelsWithParams(),
        ]);
    }
}
