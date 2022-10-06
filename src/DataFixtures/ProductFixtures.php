<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use PhpCsFixer\Finder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Traversable;

class ProductFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(
        private readonly ParameterBagInterface $parameterBag
    ) {
    }

    public function load(ObjectManager $manager)
    {
        /** @var Generator $faker */
        $faker = Factory::create();

        $faker->addProvider(new \FakerRestaurant\Provider\en_US\Restaurant($faker));

        $categories = [];
        foreach (range(0, 15) as $productIndex) {
            $categories[] = $category = new Category();
            $category->setName($faker->fruitName().' '.$faker->companySuffix());

            $manager->persist($category);
        }

        $manager->flush();

        /** @var SplFileInfo[]|Traversable $files */
        $files = (new Finder())
            ->in($this->parameterBag->get('kernel.project_dir').'/public/images/modified')
            ->name(['*.jpg', '*.png'])
            ->getIterator();

        $images = array_map(fn (SplFileInfo $file) => $file->getRelativePathname(), iterator_to_array($files));

        foreach (range(0, 100) as $productIndex) {
            $product = new Product();
            $product->setCode(strtoupper($faker->bothify('??_####')))
                ->setName($faker->foodName())
                ->setPrice($faker->numerify('##.##'))
                ->setDescription($faker->realTextBetween(150, 250))
                ->setCategory($faker->randomElement($categories))
                ->setImageUrl($faker->randomElement($images));

            $manager->persist($product);
        }

        $manager->flush();
    }
}
