<?php declare(strict_types=1);
/**
 * 17.06.2020.
 */

namespace App\Controller;

use App\Entity\Pizza;
use App\Form\PizzaType;
use App\Service\PizzaManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route(path="/pizza/{page<\d+>?1}", name="pizza_main")
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
     * @Route(path="/pizza/new", name="pizza_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $pizza = new Pizza();
        $form = $this->createForm(PizzaType::class, $pizza);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pizza = $form->getData();
            $this->manager->store($pizza);

            return $this->redirectToRoute('pizza_view', ['id' => $pizza->getId()]);
        }

        return $this->render('pizza/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/pizza/update/{id<\d+>}", name="pizza_update")
     *
     * @param string  $id
     * @param Request $request
     *
     * @return Response
     */
    public function update(string $id, Request $request): Response
    {
        $pizza = $this->manager->get($id);
        $form = $this->createForm(PizzaType::class, $pizza);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $pizza = $form->getData();
            $this->manager->update($pizza);

            return $this->redirectToRoute('pizza_view', ['id' => $pizza->getId()]);
        }

        return $this->render('pizza/update.html.twig', [
            'pizza' => $pizza,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/pizza/view/{id}", name="pizza_view", requirements={"id"="\d+"})
     *
     * @param string $id
     *
     * @return Response
     */
    public function view(string $id): Response
    {
        return $this->render('pizza/view.html.twig', ['pizza' => $this->manager->get($id)]);
    }
}
