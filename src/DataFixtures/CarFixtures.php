<?php

namespace App\DataFixtures;

use App\Entity\Color;
use App\Entity\Manufacture;
use App\Entity\Model;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Faker\UniqueGenerator;

class CarFixtures extends Fixture
{
    protected const FINAL_COUNT = 20;

    /**
     * @var Generator|UniqueGenerator
     */
    private $faker;

    /**
     * @var array|string[]
     */
    private static array $colorNames = [
        'Красный',
        'Синий',
        'Зеленый',
        'Черный',
        'Белый',
        'Серый',
        'Желтый',
        'Оранжевый',
        'Голубой',
        'Металлик',
    ];

    /**
     * CarFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = self::FINAL_COUNT; $i >= 0; --$i) {
            $name = $this->faker->state;
            $manufacture = (new Manufacture())
                ->setName($name)
            ;
            $this->addRandomModels($manager, $manufacture);
            $manager->persist($manufacture);
        }

        $manager->flush();
    }

    protected function addRandomModels(ObjectManager $manager, Manufacture $manufacture): void
    {
        $count = \random_int(4, 10);
        for ($i = $count; $i >= 0; --$i) {
            $model = (new Model())
                ->setName($this->faker->city)
                ->setPrice($this->faker->numberBetween(1000000, 9999999))
            ;

            $this->addRandomColor($manager, $model);
            $manager->persist($model);
            $manufacture->addModel($model);
        }
        $manager->flush();
    }

    protected function addRandomColor(ObjectManager $manager, Model $model): void
    {
        $count = \random_int(3, 7);
        for ($i = $count; $i >= 0; --$i) {
            $color = (new Color())
                ->setName($this->faker->randomElement(self::$colorNames))
                ->addModel($model)
            ;
            $model->addColor($color);
            $manager->persist($color);
        }

        $manager->flush();
    }
}
