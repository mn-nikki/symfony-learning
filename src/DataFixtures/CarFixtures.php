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
    protected const FINAL_COUNT = 50;

    /**
     * @var Generator|UniqueGenerator
     */
    private $faker;

    /**
     * @var array|string
     */
    private array $colorNames = [];

    /**
     * @var array|string
     */
    private array $manufactureNames = [];

    /**
     * @var array|Color
     */
    private array $colors = [];

    /**
     * CarFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $this->makeColors($manager);

        while (\count($this->manufactureNames) !== self::FINAL_COUNT) {
            $name = $this->faker->state;

            if (\in_array($name, $this->manufactureNames)) {
                continue;
            } else {
                $manufacture = (new Manufacture())
                    ->setName($name)
                ;
                $this->manufactureNames[] = $name;
            }
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

    protected function makeColors(ObjectManager $manager): void
    {
        while (\count($this->colorNames) !== 50) {
            $name = $this->faker->colorName;
            if (\in_array($name, $this->colorNames)) {
                continue;
            } else {
                $color = (new Color())
                    ->setName($name)
                ;
                $manager->persist($color);
                $this->colors[] = $color;
                $this->colorNames[] = $name;
            }
        }

        $manager->flush();
    }

    protected function addRandomColor(ObjectManager $manager, Model $model): void
    {
        $count = \random_int(3, 7);

        for ($i = $count; $i >= 0; --$i) {
            $randomKey = \random_int(0, 9);
            $color = $this->colors[$randomKey];
            $color->addModel($model);

            $model->addColor($color);
            $manager->persist($color);
        }

        $manager->flush();
    }
}
