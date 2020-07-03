<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Controller;

use App\Service\CreatePizzaServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class CreatePizzaController extends AbstractController
{
    /**
     * @var CreatePizzaServiceInterface
     */
    private CreatePizzaServiceInterface $createPizzaService;
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    public function __construct(CreatePizzaServiceInterface $createPizzaService, TranslatorInterface $translator)
    {
        $this->createPizzaService = $createPizzaService;
        $this->translator = $translator;
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
