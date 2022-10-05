<?php

namespace App\Controller;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/cart')]
class OrderController extends AbstractController
{

    #[Route('/checkout', name: 'cart.checkout', methods: ['GET', 'POST'])]
    public function new(Request $request, OrderRepository $orderRepository): Response
    {
        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $order->setUser($this->getUser());
        $form->handleRequest($request);

        $isSubmitted = 'false';

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getUser()->addOrder($order);
            $orderRepository->add($order, true);
            $this->addFlash('success', 'Order created successfully!');
            $this->addFlash('events', 'cart.clear');

            // @fixme replace with a redirect to home
            return $this->redirectToRoute('home', [], Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted() && !$form->isValid()) {
            $isSubmitted = 'true';
        }

        return $this->renderForm('order/new.html.twig', [
            'order' => $order,
            'form' => $form,
            'isSubmitted' => $isSubmitted,
        ]);
    }
}
