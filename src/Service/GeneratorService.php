<?php

namespace App\Service;

use App\Entity\Cat;
use App\Repository\CatRepository;
use Doctrine\ORM\EntityManagerInterface;

class GeneratorService
{
    private const MAX_AGE = 30;
    private const MIN_AGE = 0;

    private const COLORS = [
        'черный',
        'белый',
        'серый',
        'полосатый',
        'коричневый',
        'пятнистый'
    ];


    public function __construct(
        private CatRepository          $repository,
        private EntityManagerInterface $em,
    )
    {
    }

    public function generate(): void
    {
        // https://github.com/Harrix/Russian-Nouns/releases/download/v2.0/russian_nouns_v2.0.zip
        $content = file_get_contents('path.json');
        $names = json_decode($content, true);

        $colorsAmount = count(self::COLORS) - 1;

        foreach ($names as $name => $v) {
            $cat = new Cat;
            $age = rand(self::MIN_AGE, self::MAX_AGE);
            $colorIndex = rand(0, $colorsAmount);
            $color = self::COLORS[$colorIndex];
            $cat
                ->setName($name)
                ->setAge($age)
                ->setColor($color);

            $this->repository->save($cat);

        }

        $this->em->flush();
    }
}

