<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Model;
use App\Service\CarManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/")
 */
class CarController
{
    private CarManagerInterface $manager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;

    /**
     * CarController constructor.
     *
     * @param CarManagerInterface $manager
     * @param SerializerInterface $serializer
     * @param ValidatorInterface  $validator
     */
    public function __construct(CarManagerInterface $manager, SerializerInterface $serializer, ValidatorInterface $validator)
    {
        $this->manager = $manager;
        $this->serializer = $serializer;
        $this->validator = $validator;
    }

    /**
     * @Route("models/{page<\d+>?1}", name="api_models")
     *
     * @param int $page
     *
     * @return Response
     */
    public function index(int $page = 1): Response
    {
        $data = $this->manager->getRepository()->getPager($page, 10);
        $format = 'json';

        $serializedData = $this->serializer->serialize($data, $format, [
            'groups' => 'display',
        ]);

        return new Response($serializedData, Response::HTTP_OK, [
            'Content-Type' => \sprintf('%s; charset=utf-8', $format),
        ]);
    }

    /**
     * @Route("model/show/{id<\d+>?1}", name="api_model_show")
     *
     * @param int $id
     *
     * @return Response
     */
    public function show(int $id = 1): Response
    {
        $model = $this->manager->getRepository()->find($id);
        $format = 'json';
        $status = Response::HTTP_OK;

        $data = $this->serializer->serialize($model, $format, [
            'groups' => 'display',
        ]);

        if ($model === null) {
            $status = Response::HTTP_NOT_FOUND;
            $data = \sprintf('Model with id = %s not found', $id);
        }

        return new Response($data, $status, [
            'Content-Type' => \sprintf('%s; charset=utf-8', $format),
        ]);
    }

    /**
     * @Route("model/validate", name="api_json_validate", methods={"GET"})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function validate(Request $request): Response
    {
        $format = 'json';
        $data = $this->serializer->deserialize($request->get('json'), Model::class, $format);
        $errors = $this->validator->validate($data);
        $status = Response::HTTP_OK;

        if (\count($errors) > 0) {
            $data = (string) $errors;
            $status = Response::HTTP_BAD_REQUEST;
            $format = 'text/html';
        } else {
            $data = $this->serializer->serialize($data, $format, [
                'groups' => 'display',
            ]);
        }

        return new Response($data, $status, [
            'Content-Type' => \sprintf('%s; charset=utf-8', $format),
        ]);
    }
}
