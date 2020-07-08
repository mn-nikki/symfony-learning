<?php declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Model;
use App\Service\CarManagerInterface;
use Swagger\Annotations as SWG;
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
     * @Route("model/show/{id<\d+>?1}", name="api_model_show", methods={"GET", "POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Get Model by id",
     *     @\Nelmio\ApiDocBundle\Annotation\Model(type=Model::class)
     * )
     * @SWG\Response(
     *     response=404,
     *     description="Model witch this id not found",
     * )
     * @SWG\Tag(name="model by id")
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
     * @SWG\Response(
     *     response=200,
     *     description="Validate json data",
     *     @\Nelmio\ApiDocBundle\Annotation\Model(type=Model::class)
     * )
     * @SWG\Response(
     *     response=400,
     *     description="Data is not valid",
     * )
     *
     * @SWG\Parameter(
     *     parameter="json",
     *     name="json",
     *     in="query",
     *     type="string",
     *     description="data for validate",
     * )
     * @SWG\Tag(name="validate json")
     *
     * @param Request $request
     *
     * @return Response
     *
     * @throws \Exception
     */
    public function validate(Request $request): Response
    {
        if (!$request->query->has('json')) {
            throw new \Exception('The request must have a json parameter');
        }

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
