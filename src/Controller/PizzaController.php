<?php declare(strict_types=1);
/**
 * 17.06.2020
 */


namespace App\Controller;


use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PizzaController extends AbstractController
{
    private PizzaRepository $pizzaRepository;
    private TranslatorInterface $translator;

    public function __construct(PizzaRepository $pizzaRepository, TranslatorInterface $translator)
    {
        $this->pizzaRepository = $pizzaRepository;
        $this->translator = $translator;
    }

    /**
     * @Route(path="/pizza/{page<\d+>?1}")
     *
     * @param int|null $page
     * @return Response
     */
    public function index(?int $page = null): Response
    {
        return $this->render('pizza/index.html.twig', [
            'data' => $this->pizzaRepository->findAll(),
            'title' => $this->translator->trans('pizza.title')
        ]);
    }

    /**
     * @Route(path="/pizza/parts/{count<\d+>}")
     *
     * @param int $count
     * @return Response
     */
    public function withIngredients(int $count): Response
    {
        return $this->render('pizza/index.html.twig', [
            'data' => $this->pizzaRepository->withNIngredients($count),
        ]);
    }
}
