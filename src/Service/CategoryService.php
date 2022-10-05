<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class CategoryService
{
    public function __construct(
        private RequestStack $requestStack,
        private CategoryRepository $categoryRepo,
        private PaginatorInterface $paginator,
        private ContainerBagInterface $bag,
    ) {
    }

    public function getPaginatedCategories()
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = $this->bag->get('app.paginator');
        $categoryQuery = $this->categoryRepo->findForPagination();

        return $this->paginator->paginate($categoryQuery, $page, $limit);
    }
}
