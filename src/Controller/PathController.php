<?php


namespace App\Controller;


use App\Entity\BlogPost;
use Symfony\Component\HttpFoundation\Response;
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
            'var' => sprintf("Hello world %s!", __METHOD__),
        ]);
    }

    /**
     * @Route("/path/number/{number}", name="show_number", requirements={"page"="\d+"}, methods={"GET", "HEAD"})
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
}
