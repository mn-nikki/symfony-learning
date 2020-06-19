<?php declare(strict_types=1);
/**
 * 17.06.2020.
 */

namespace App\Controller;

use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PizzaController extends AbstractController
{
    private PizzaRepository $pizzaRepository;

    public function __construct(PizzaRepository $pizzaRepository)
    {
        $this->pizzaRepository = $pizzaRepository;
    }

    /**
     * @Route(path="/pizza/{page<\d+>?1}")
     *
     * @param int|null $page
     *
     * @return Response
     */
    public function index(?int $page = null): Response
    {
        return $this->render('pizza/index.html.twig', [
            'data' => $this->pizzaRepository->findAll(),
        ]);
    }

    /**
     * @Route(path="/pizza/parts/{count<\d+>}")
     *
     * @param int $count
     *
     * @return Response
     */
    public function withIngredients(int $count): Response
    {
        return $this->render('pizza/index.html.twig', [
            'data' => $this->pizzaRepository->withNIngredients($count),
        ]);
    }
}
