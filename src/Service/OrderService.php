<?php

namespace App\Service;

use App\Repository\OrderRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderService
{
    public function __construct(
        private RequestStack $requestStack,
        private OrderRepository $orderRepository,
        private PaginatorInterface $paginator,
        private ContainerBagInterface $bag,
    ){ }

    public function getPaginatedOrders(array $filters){
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt("page", 1);
        $limit = $this->bag->get('app.paginator');
        $categoryQuery = $this->orderRepository->findForPagination($filters);

        return $this->paginator->paginate($categoryQuery, $page, $limit);
    }

}