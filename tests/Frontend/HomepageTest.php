<?php

namespace App\Tests\Frontend;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DomCrawler\Crawler;

class HomepageTest extends WebTestCase
{
    public function testHomepage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertPageTitleSame('Internshop');
        $this->assertHasNavigation();
        $this->assertHasFooter();
        $this->assertHasProducts();
        $this->assertHasWorkingLinks($crawler);
    }

    public function testDetailspage(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/details/14');

        $this->assertResponseIsSuccessful();
        $this->assertHasNavigation();
        $this->assertHasFooter();
        $this->assertHasProduct();
    }

    public function assertHasNavigation(): void
    {
        $this->assertSelectorTextContains('#navbar_top', 'Home');
        $this->assertSelectorTextContains('#navbar_top', 'Contacts');
        $this->assertSelectorExists('#navbar_top input[type="search"]');
    }

    public function assertHasFooter(): void
    {
        $this->assertSelectorTextContains('footer', 'Links');
        $this->assertSelectorTextContains('footer', 'Address');
        $this->assertSelectorTextContains('footer', 'Contacts');
    }

    public function assertHasProducts(): void
    {
        $this->assertSelectorExists('.card .product-img');
        $this->assertSelectorExists('.card .card-body .product-name');
        $this->assertSelectorExists('.card .card-body .product-description');
    }

    public function assertHasWorkingLinks(Crawler $crawler): void
    {
        $homeLinks = $crawler->selectLink('Home')->links();
        foreach ($homeLinks as $link) {
            self::assertEquals($crawler->getUri(), $link->getUri());
        }

        $contactsLink = $crawler->selectLink('Contacts')->link();
        self::assertEquals('http://localhost/contacts', $contactsLink->getUri());
    }

    public function assertHasProduct(): void
    {
        $this->assertSelectorExists('.col-5 .product-img');
        $this->assertSelectorExists('.card .card-header .product-name');
        $this->assertSelectorExists('.card .card-body .product-category');
        $this->assertSelectorExists('.card .card-body .product-code');
        $this->assertSelectorExists('.card .card-body .product-price');
        $this->assertSelectorExists('.card .card-body .product-description');
    }
}