<?php


namespace App\Services;


use App\Controller\PathController;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouteCollection;

class ExtraLoader extends Loader
{
    /**
     * @param mixed $resource
     * @param string|null $type
     * @return RouteCollection
     */
    public function load($resource, string $type = null)
    {
        $routes = new RouteCollection();
        $path = '/extra';
        $defaults = [
            '_controller' => sprintf('%s:extra', PathController::class),
        ];

        return $routes;
    }

    /**
     * @param mixed $resource
     * @param string|null $type
     * @return bool|void
     */
    public function supports($resource, string $type = null)
    {
        return $type === 'extra';
    }
}
