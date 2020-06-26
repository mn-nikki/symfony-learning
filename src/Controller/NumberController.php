<?php declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class NumberController extends AbstractController
{
    /**
     * @Route(path="/number/{number<\d+>?1}", name="number_route")
     * @param int $number
     * @return Response
     */
    public function index(int $number = 1): Response
    {
        return $this->render('path/number.html.twig', [
            'number' => $number,
        ]);
    }
}
