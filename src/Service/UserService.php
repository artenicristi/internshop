<?php

namespace App\Service;

use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class UserService
{
    public function __construct(
        private RequestStack $requestStack,
        private UserRepository $userRepo,
        private PaginatorInterface $paginator,
        private ContainerBagInterface $bag,
    ) {
    }

    public function getPaginatedUsers()
    {
        $request = $this->requestStack->getMainRequest();
        $page = $request->query->getInt('page', 1);
        $limit = $this->bag->get('app.paginator');
        $productsQuery = $this->userRepo->findForPagination();

        return $this->paginator->paginate($productsQuery, $page, $limit);
    }
}
