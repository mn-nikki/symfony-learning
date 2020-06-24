<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\CreatePizzaServiceInterface;
use App\Service\PizzaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PizzaController extends AbstractController
{
    private TranslatorInterface $translator;
    private PizzaManagerInterface $manager;
    /**
     * @var CreatePizzaServiceInterface
     */
    private CreatePizzaServiceInterface $createPizzaService;

    public function __construct(PizzaManagerInterface $manager, TranslatorInterface $translator, CreatePizzaServiceInterface $createPizzaService)
    {
        $this->translator = $translator;
        $this->manager = $manager;
        $this->createPizzaService = $createPizzaService;
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
            'data' => $this->manager->getRepository()->findAll(),
            'title' => $this->translator->trans('pizza.title'),
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
            'data' => $this->manager->getRepository()->withNIngredients($count),
        ]);
    }

    /**
     * @Route(path="/pizza/create/{title}/{description}/{diameter<\d+>}")
     *
     * @param string $title
     * @param string $description
     * @param int    $diameter
     *
     * @return Response
     */
    public function createNewPizza(string $title, string $description, int $diameter): Response
    {
        $pizza = $this->createPizzaService->createNewPizza($title, $description, $diameter);

        return $this->render('pizza/index.html.twig', [
            'data' => [$pizza],
            'title' => $this->translator->trans('pizza.title'),
        ]);
    }
}
