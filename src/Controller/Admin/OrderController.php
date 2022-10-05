<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Form\OrderType;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Service\OrderService;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/order')]
class OrderController extends AbstractController
{
    #[Route('/', name: 'admin.order.index', methods: ['GET'])]
    public function index(OrderService $orderService, Request $request): Response
    {
        $filters = array_filter($request->query->all(), 'strlen');

        return $this->render('order/index.html.twig', [
            'orders' => $orderService->getPaginatedOrders($filters),
        ]);
    }

    #[Route('/{id}', name: 'admin.order.show', requirements : ['id' => '\d+'], methods: ['GET'])]
    public function show(Order $order): Response
    {
        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin.order.edit',requirements : ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $orderRepository->add($order, true);

            return $this->redirectToRoute('admin.order.index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('order/edit.html.twig', [
            'order' => $order,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'admin.order.delete', requirements : ['id' => '\d+'], methods: ['POST'])]
    public function delete(Request $request, Order $order, OrderRepository $orderRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$order->getId(), $request->request->get('_token'))) {
            $orderRepository->remove($order, true);
        }

        return $this->redirectToRoute('admin.order.index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/status', name: 'admin.order.status', requirements : ['id' => '\d+'], methods: ['POST'])]
    public function changeStatus(Request $request, Order $order, OrderItemRepository $orderItemRepository, OrderRepository $orderRepository, MailerInterface $mailer)
    {
        $newStatus = $request->request->get('status');
        if (!in_array($newStatus, Order::STATUSES)) {
            throw new BadRequestHttpException('This status is not allowed');
        }

        $order->setStatus($newStatus);
        $orderRepository->add($order, true);

        $email = (new TemplatedEmail())
            ->from('project@gmail.com')
            ->to('admin@internshop.com')
            ->subject('Your order status was changed.')
            ->htmlTemplate('order/order_email_status.html.twig')
            ->context([
                'status' => $newStatus,
                'order' => $order,
            ]);
        $mailer->send($email);

        return $this->redirectToRoute('admin.order.index');
    }

}
