<?php

namespace App\Tests\Admin;

use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class AdminPageTest extends WebTestCase
{
    /**
     * @throws \Exception
     */
    public function testProductPage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@test.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/product/');
        $this->assertResponseIsSuccessful();
        $this->assertHasNavigation();
        $this->assertProductHasWorkingLinks($crawler);
        $this->assertProductHasTable();
    }

    /**
     * @throws \Exception
     */
    public function testUserPage():void
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@test.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/user/');
        $this->assertResponseIsSuccessful();
        $this->assertHasNavigation();
        $this->assertUserHasWorkingLinks($crawler);
        $this->assertUserHasTable();
    }

    /**
     * @throws \Exception
     */
    public function testCategoryPage()
    {
        $client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $testUser = $userRepository->findOneByEmail('admin@test.com');
        $client->loginUser($testUser);
        $crawler = $client->request('GET', '/admin/category/');
        $this->assertResponseIsSuccessful();
        $this->assertHasNavigation();
        $this->assertCategoryHasWorkingLinks($crawler);
    }

    public function assertHasNavigation():void{
        $this->assertSelectorTextContains('#navbar-left', 'Main menu');
        $this->assertSelectorTextContains('#navbar-left', 'Products');
        $this->assertSelectorTextContains('#navbar-left', 'Categories');
        $this->assertSelectorTextContains('#navbar-left', 'Users');
        $this->assertSelectorTextContains('#navbar-left', 'Orders');
    }

    public function assertProductHasWorkingLinks(Crawler $crawler): void
    {
        $productsLinks = $crawler->selectLink('Products')->links();
        foreach ($productsLinks as $link) {
            self::assertEquals($crawler->getUri(), $link->getUri());
        }

        $usersLink = $crawler->selectLink('Users')->link();
        self::assertTrue(str_contains($usersLink->getUri(), '/admin/user/'));
        $categoriesLink = $crawler->selectLink('Categories')->link();
        self::assertTrue(str_contains($categoriesLink->getUri(), '/admin/category/'));
        $createProductLink = $crawler->selectLink('Create product')->link();
        self::assertTrue(str_contains($createProductLink->getUri(), '/admin/product/'));

    }

    public function assertCategoryHasWorkingLinks(Crawler $crawler): void
    {
        $productsLinks = $crawler->selectLink('Categories')->links();
        foreach ($productsLinks as $link) {
            self::assertEquals($crawler->getUri(), $link->getUri());
        }

        $usersLink = $crawler->selectLink('Users')->link();
        self::assertTrue(str_contains($usersLink->getUri(), '/admin/user/'));
        $productsLink = $crawler->selectLink('Products')->link();
        self::assertTrue(str_contains($productsLink->getUri(), '/admin/product/'));
        $createProductLink = $crawler->selectLink('Create category')->link();
        self::assertTrue(str_contains($createProductLink->getUri(), '/admin/category/new'));
    }

    public function assertUserHasWorkingLinks(Crawler $crawler): void
    {
        $usersLinks = $crawler->selectLink('Users')->links();
        foreach ($usersLinks as $link) {
            self::assertEquals($crawler->getUri(), $link->getUri());
        }

        $productsLink = $crawler->selectLink('Products')->link();
        self::assertTrue(str_contains($productsLink->getUri(), '/admin/product/'));
        $categoriesLink = $crawler->selectLink('Categories')->link();
        self::assertTrue(str_contains($categoriesLink->getUri(), '/admin/category/'));
        $createUserLink = $crawler->selectLink('Create User')->link();
        self::assertTrue(str_contains($createUserLink->getUri(), '/admin/user/new'));
    }

    public function assertUserHasTable():void{
        $this->assertSelectorTextSame('#title', 'Users');
        $this->assertSelectorTextcontains('#user-table', 'Id');
        $this->assertSelectorTextContains('#user-table', 'Email');
        $this->assertSelectorTextContains('#user-table', 'Role');
        $this->assertSelectorTextContains('#user-table', 'User Name');
        $this->assertSelectorTextContains('#user-table', 'CreatedAt');
        $this->assertSelectorTextContains('#user-table', 'UpdatedAt');
        $this->assertSelectorTextContains('#user-table', 'Actions');
        $this->assertSelectorTextNotContains("#user-table", 'Password');
    }


    public function assertProductHasTable():void{
        $this->assertSelectorTextSame('#title', 'Products');
        $this->assertSelectorTextcontains('#product-table', 'Id');
        $this->assertSelectorTextContains('#product-table', 'Name');
        $this->assertSelectorTextContains('#product-table', 'Price');
        $this->assertSelectorTextContains('#product-table', 'Code');
        $this->assertSelectorTextContains("#product-table", 'Description');
        $this->assertSelectorTextContains("#product-table", 'Category');
        $this->assertSelectorTextContains('#product-table', 'CreatedAt');
        $this->assertSelectorTextContains('#product-table', 'UpdatedAt');
        $this->assertSelectorTextContains('#product-table', 'Actions');
    }

    public function assertCategoryHasTable():void{
        $this->assertSelectorTextSame('#title', 'Categories');
        $this->assertSelectorTextcontains('#category-table', 'Id');
        $this->assertSelectorTextContains('#category-table', 'Name');
        $this->assertSelectorTextContains("#category-table", 'ParentCategory');
        $this->assertSelectorTextContains('#category-table', 'CreatedAt');
        $this->assertSelectorTextContains('#category-table', 'UpdatedAt');
        $this->assertSelectorTextContains('#category-table', 'Actions');
    }

}