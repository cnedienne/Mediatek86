<?php

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class FunctionalTest extends WebTestCase
{
    public function testHomepageIsAccessible()
    {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Homepage should be accessible');
       
    }

    public function testSortingFunctionality()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/formations/tri/title/ASC');

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Sorting page should be accessible');
        $this->assertEquals('Android Studio (complément n°1) : Navigation Drawer et Fragment', $crawler->filter('table tbody tr:first-child td:first-child')->text(), 'First row should contain the expected title');
    }

    public function testFilteringFunctionality()
    {
        $client = static::createClient();
        $crawler = $client->request('POST', '/formations/recherche/title', ['recherche' => 'Déploiement']);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Filtering page should be accessible');
        $this->assertGreaterThan(0, $crawler->filter('table tbody tr')->count(), 'There should be at least one result');
        $this->assertEquals('Eclipse n°8 : Déploiement', $crawler->filter('table tbody tr:first-child td:first-child')->text(), 'First row should contain the expected title');
    }

    public function testLinkClick()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        // Ensure the link with text "Formations" exists
        $this->assertGreaterThan(0, $crawler->filter('a:contains("Formations")')->count(), 'Link "Formations" should exist');

        $link = $crawler->selectLink('Formations')->link();
        $client->click($link);

        $this->assertEquals(200, $client->getResponse()->getStatusCode(), 'Formations page should be accessible');
        $this->assertStringContainsString('Formations', $client->getResponse()->getContent(), 'Formations page content should contain "Formations"');
    }
}