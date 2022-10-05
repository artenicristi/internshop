<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Form\ProductContactType;
use App\Repository\CategoryRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use App\Service\ProductService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class HomePageController extends AbstractController
{

    #[Route('/', 'home', methods: 'GET')]
    public function getHomePage(Request $request, ProductService $productService, CategoryRepository $categoryRepository): Response
    {
        $filters = array_filter($request->query->all(), 'strlen');

        return $this->render('frontend/home_page/home_page.html.twig',
            [
                'productList' => array_chunk(iterator_to_array($productService->getPaginatedProducts($filters)),3),
                'categories' => $categoryRepository->findAll(),
                'paginator'=>$productService->getPaginatedProducts($filters),
                'name' => count($filters) == 1 ? $filters : null
            ]
        );
    }

    #[Route('/details/{id}', 'product.details', methods: ['GET', 'POST'])]
    public function productDetails(Product $product, Request $request, MailerInterface $mailer): Response
    {
        $form = $this->createForm(ProductContactType::class);
        $contact = $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $email = (new TemplatedEmail())
                ->from('project@gmail.com')
                ->to('admin@internshop.com')
                ->subject('New Order')
                ->htmlTemplate('frontend/product/order_email.html.twig')
                ->context([
                    'product_code' => $product->getCode(),
                    'product' => $product,
                    'mail' => $contact->get('email')->getData(),
                    'text' => $contact->get('text')->getData(),
                ]);
            $mailer->send($email);
            $this->addFlash('success', 'Order sent');
            return $this->redirectToRoute('product.details', ['id' => $product->getId()]);
        }
        return $this->render('frontend/product/details.html.twig', [
            'product' => $product,
            'form' => $form->createView()
        ]);
    }

    #[Route('/contacts', 'contacts', methods: 'GET')]
    public function getContacts(): Response
    {
        return $this->render('frontend/contacts_page.html.twig');
    }

    #[Route('/order/new', name: 'app_order_new', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderRepository $orderRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->add($order, true);

            $this->redirectToRoute('app_order_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('cabinet/{id}/status', name: 'user.order.status', requirements : ['id' => '\d+'], methods: ['POST'])]
    public function changeStatus(Request $request, Order $order, OrderItemRepository $orderItemRepository, OrderRepository $orderRepository, MailerInterface $mailer)
    {
        $newStatus = $request->request->get('status');
        if (!in_array($newStatus, ['canceled'])) {
            throw new BadRequestHttpException('This status is not allowed');
        }
        $order->setStatus($newStatus);
        $orderRepository->add($order, true);

        $email = (new TemplatedEmail())
            ->from('project@gmail.com')
            ->to('admin@internshop.com')
            ->subject('New Order')
            ->htmlTemplate('order/order_email_status.html.twig')
            ->context([
                'status' => $newStatus,
            ]);
        $mailer->send($email);

        return $this->redirectToRoute('user.cabinet');
    }



    #[Route('/cabinet', 'user.cabinet', methods:'GET')]
    public function getUserCabinet(OrderService $orderService, Request $request):Response{
        $filters = array_filter($request->query->all(), 'strlen');
        $filters['user']= $this->getUser();

        return $this->render('frontend/user_cabinet/user_cabinet_page.html.twig',[
            'orders' => $orderService->getPaginatedOrders($filters),
        ]);

    }

    #[Route('/order/{id}', 'order.details', methods:'GET')]
    public function getOrderDetails(Order $order):Response{

        return $this->render('frontend/user_cabinet/order_details.html.twig',[
            'order' => $order,
        ]);
    }

}

