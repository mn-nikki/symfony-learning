<?php declare(strict_types=1);

namespace App\Controller;

use App\Entity\Model;
use App\Form\ModelType;
use App\Service\CarManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CarController extends AbstractController
{
    private CarManagerInterface $manager;
    private LoggerInterface $logger;

    public function __construct(CarManagerInterface $manager, LoggerInterface $logger)
    {
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     * @Route(path="/path/models/{page<\d+>?1}", name="model_index")
     *
     * @param int $page
     * @param int $count
     *
     * @return Response
     */
    public function index(int $page = 1, int $count = 10): Response
    {
        $models = $this->manager->getRepository()->getPager($page, $count);
        $maxPage = \ceil($models->count() / $count);

        if ($page > $maxPage) {
            $this->logger->critical(\sprintf('There is no page with this number - %s.', $page));
        }

        return $this->render('path/index.html.twig', [
            'data' => $models,
            'page' => $page,
            'maxPage' => $maxPage,
        ]);
    }

    /**
     * @Route(path="/path/model/new", name="model_create")
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request): Response
    {
        $model = new Model;
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $this->manager->store($model);

            return $this->redirectToRoute('model_view', ['id' => $model->getId()]);
        }

        return $this->render('path/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/path/model/update/{id<\d+>}", name="model_update")
     *
     * @param int     $id
     * @param Request $request
     *
     * @return Response
     */
    public function update(int $id, Request $request): Response
    {
        $model = $this->manager->getRepository()->find($id);
        $form = $this->createForm(ModelType::class, $model);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $model = $form->getData();
            $this->manager->update($model);

            return $this->redirectToRoute('model_view', ['id' => $model->getId()]);
        }

        return $this->render('path/update.html.twig', [
            'model' => $model,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route(path="/path/model/remove/{id<\d+>}", name="model_remove")
     *
     * @param int $id
     *
     * @return Response
     */
    public function remove(int $id): Response
    {
        $model = $this->manager->getRepository()->find($id);
        $this->manager->delete($model);

        return $this->redirectToRoute('model_index');
    }

    /**
     * @Route(path="/path/model/view/{id}", name="model_view" ,requirements={"id"="\d+"})
     *
     * @param int $id
     *
     * @return Response
     */
    public function view(int $id): Response
    {
        $model = $this->manager->getRepository()->find($id);

        return $this->render('path/view.html.twig', [
            'model' => $model,
        ]);
    }
}
