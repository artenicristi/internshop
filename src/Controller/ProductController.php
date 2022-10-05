<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\ImageUploader;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin')]
class ProductController extends AbstractController
{
    #[Route('/', name: 'app_admin', methods: ['GET'])]
    public function adminRedirect(): Response
    {
        return $this->redirectToRoute('app_product_index');
    }

    #[Route('/product', name: 'app_product_index', methods: ['GET'])]
    public function index(ProductService $productService, CategoryRepository $categoryRepository, Request $request): Response
    {
        $filters = array_filter($request->query->all(), 'strlen');

        return $this->render('product/index.html.twig', [
            'products' => $productService->getPaginatedProducts($filters),
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/product/new', name: 'app_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ProductRepository $productRepository, ImageUploader $imageUploader): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        $isSubmitted = 'false';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageUrl')->getData();

            if ($file) {
                $filename = $imageUploader->upload($file);
                $product->setImageUrl($filename);
            }

            $product->setCreatedAt(new \DateTimeImmutable());
            $productRepository->add($product, true);

            return $this->redirectToRoute('app_product_index', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $isSubmitted = 'true';
        }

        return $this->renderForm('product/new.html.twig', [
            'product' => $product,
            'form' => $form,
            'action' => 'add',
            'isSubmitted' => $isSubmitted,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('product/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/product/{id}/edit', name: 'app_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product, ProductRepository $productRepository, ImageUploader $imageUploader): Response
    {
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if (!$form->isSubmitted()) {
            $request->getSession()->set('products.edit.redirect_to', $request->headers->get('referer'));
        }

        $isSubmitted = 'false';

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageUrl')->getData();

            if ($file) {
                $filename = $imageUploader->upload($file);
                $product->setImageUrl($filename);
            }

            $product->setUpdatedAt(new \DateTimeImmutable());
            $productRepository->add($product, true);

            $redirectTo = $request->getSession()->get('products.edit.redirect_to');
            $request->getSession()->remove('products.edit.redirect_to');

            return $this->redirect($redirectTo);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $isSubmitted = 'true';
        }

        return $this->renderForm('product/edit.html.twig', [
            'product' => $product,
            'form' => $form,
            'action' => 'edit',
            'isSubmitted' => $isSubmitted,
        ]);
    }

    #[Route('/product/{id}', name: 'app_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, ProductRepository $productRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $productRepository->remove($product, true);
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
