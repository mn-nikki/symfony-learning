<?php
/**
 * 24.06.2020.
 */

declare(strict_types=1);

namespace App\Service;

use App\Entity\Ingredient;
use App\Entity\ReceiptPart;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectRepository;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\Translation\Exception\NotFoundResourceException;

class ReceiptPartGenerateService implements ReceiptPartGenerateServiceInterface
{
    /**
     * @var ObjectRepository
     */
    private ObjectRepository $ingredientRepository;

    /**
     * @var Generator
     */
    private Generator $faker;

    public function __construct(EntityManagerInterface $manager)
    {
        $this->faker = Factory::create();
        $this->ingredientRepository = $manager->getRepository(Ingredient::class);
    }

    /**
     * @param string $ingredient
     * @return ReceiptPart
     */
    public function generate(string $ingredient): ReceiptPart
    {
        $ingredientEntity = $this->ingredientRepository->findOneBy(['title' => $ingredient]);

        if (null === $ingredientEntity) {
            throw new NotFoundResourceException(\sprintf('Ingredient %s not found', $ingredient));
        }

        return (new ReceiptPart())
            ->setIngredient($ingredientEntity)
            ->setWeight($this->faker->randomNumber());
    }
}
