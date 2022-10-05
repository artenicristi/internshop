<?php

namespace App\Service;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProductService
{
    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepo,
        private PaginatorInterface $paginator,
        private ContainerBagInterface $bag,
    ) {
    }

    public function getPaginatedProducts(array $filters)
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = $this->bag->get('app.paginator');
        $productsQuery = $this->productRepo->findForPagination($filters);

        return $this->paginator->paginate($productsQuery, $page, $limit);
    }
}
