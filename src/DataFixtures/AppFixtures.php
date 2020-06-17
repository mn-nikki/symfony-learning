<?php

namespace App\DataFixtures;

use App\Entity\Ingredient;
use App\Entity\Pizza;
use App\Entity\ReceiptPart;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\UniqueGenerator;

class AppFixtures extends Fixture
{
    protected const FINAL_COUNT = 102;

    /**
     * @var Generator|UniqueGenerator
     */
    private $faker;

    protected static array $food = [
        'Олливки',
        'Маслины',
        'Ананасы',
        'Бекон',
        'Салями',
        'Грибы',
        'Курица',
        'Соленые огурцы',
        'Помидоры',
        'Сыр',
    ];
    protected array $ingredients = [];

    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->makeIngredients($manager);

        for ($i = self::FINAL_COUNT; $i >= 0; $i--) {
            $pizza = (new Pizza())
                ->setTitle($this->faker->colorName)
                ->setDescription($this->faker->sentence)
                ->setDiameter($this->faker->randomElement([30, 40, 60]))
            ;
            $this->addRandomParts($pizza);
            $manager->persist($pizza);
        }

        $manager->flush();
    }

    protected function addRandomParts(Pizza $pizza): void
    {
        $count = \random_int(2, 9);
        for ($i = $count; $i >= 0; $i--) {
            $ingredient = $this->faker->randomElement($this->ingredients);
            $part = $this->makeReceiptPart($ingredient);
            $pizza->addPart($part);
        }
    }

    protected function makeIngredients(ObjectManager $manager): void
    {
        foreach (self::$food as $item) {
            $ingredient = (new Ingredient())
                ->setTitle($item)
                ->setPrice($this->faker->randomDigit);

            $manager->persist($ingredient);
            $this->ingredients[] = $ingredient;
        }

        $manager->flush();
    }

    protected function makeReceiptPart(Ingredient $ingredient): ReceiptPart
    {
        return (new ReceiptPart())
            ->setIngredient($ingredient)
            ->setWeight($this->faker->randomNumber());
    }
}
