<?php declare(strict_types=1);

namespace App\Controller;

use App\Service\PizzaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PizzaController extends AbstractController
{
    private TranslatorInterface $translator;
    private PizzaManagerInterface $manager;

    public function __construct(PizzaManagerInterface $manager, TranslatorInterface $translator)
    {
        $this->translator = $translator;
        $this->manager = $manager;
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
}
