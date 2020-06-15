<?php


namespace App\Controller;


use Symfony\Component\HttpFoundation\{Request,Response};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PathController extends AbstractController
{
    /**
     * @Route("/path", name="path_index")
     *
     * @return Response
     */
    public function index() : Response
    {
        return $this->render('path/index.html.twig', [
            'var' => \sprintf("Hello, action name is %s!", __METHOD__),
        ]);
    }

    /**
     * @Route("/path/number/{number<\d+>}", name="show_number", methods={"GET", "HEAD"})
     *
     * @param int $number
     * @return Response
     */
    public function showNumber(int $number = 1) : Response
    {
        return $this->render('path/number.html.twig', [
            'number' => $number,
            'url' => $this->generateUrl('path_index', [],UrlGeneratorInterface::ABSOLUTE_URL),
        ]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function debug(Request $request) : Response
    {
        if($request->query->has('debug')) $request->query->set('debug', true);
        else $request->query->set('debug', false);

        return $this->render("path/debug.html.twig", array(
            'query' => $request->query->all(),
            'attributes' => $request->attributes->all(),
            'server' => $request->server->all(),
            'headers' => $request->headers->all(),
            'action' => \sprintf("Action name: %s", __METHOD__),
        ));
    }
}
