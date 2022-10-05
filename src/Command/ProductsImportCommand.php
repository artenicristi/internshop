<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ImageOptimizer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[AsCommand(
    name: 'products:import',
    description: 'Add a product to the web-shop',
)]
class ProductsImportCommand extends Command
{
    public function __construct(
        private readonly ProductRepository $productRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly ImageOptimizer $imageOptimizer,
        private readonly ParameterBagInterface $parameterBag
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('fileName', InputArgument::REQUIRED, 'Path to the JSON product content');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fileName = $input->getArgument('fileName');

        if ($fileName) {
            $io->note(sprintf('You passed an argument: %s', $fileName));
        }

        if (str_starts_with($fileName, './')) {
            $fileName = substr($fileName, 2);
        }

        $filepath = getcwd().DIRECTORY_SEPARATOR.$fileName;

        if (!file_exists($filepath)) {
            $io->error('This file does not exist !!');

            return Command::FAILURE;
        }

        $products = json_decode(file_get_contents($filepath), true);
        $addedProducts = 0;

        foreach ($products as $productData) {
            $category = $this->categoryRepository->findOneBy(['name' => $productData['category']]);
            if ($this->productRepository->findOneBy(['code' => $productData['code']])) {
                continue;
            }

            if (!$category) {
                $category = $this->createCategory($productData['category']);
                $this->categoryRepository->add($category);
            }

            // accumulates products in the repository
            $this->productRepository->add($product = $this->createProduct($productData, $category));

            $loadedDirectory = $this->parameterBag->get('kernel.project_dir').'/public/images/loaded';
            $targetDirectory = $this->parameterBag->get('kernel.project_dir').'/public/images/modified';

            $this->imageOptimizer->resize($loadedDirectory, $targetDirectory, $product->getImageUrl());

            ++$addedProducts;
            $output->writeln("Product added {$product->getCode()}");
        }

        // saves all accumulated products
        $this->entityManager->flush();

        $io->success('You have successfully added '.$addedProducts.' products.');

        return Command::SUCCESS;
    }

    public function createProduct(array $productData, ?Category $category): Product
    {
        return (new Product())
            ->setName($productData['name'])
            ->setPrice((string) ($productData['price'] ?? 0))
            ->setCode($productData['code'])
            ->setDescription($productData['description'])
            ->setImageUrl($productData['imageUrl'])
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(null)
            ->setCategory($category);
    }

    public function createCategory($categoryData): Category
    {
        return (new Category())
            ->setParentId(null)
            ->setName($categoryData)
            ->setCreatedAt(new \DateTimeImmutable())
            ->setUpdatedAt(null);
    }
}
