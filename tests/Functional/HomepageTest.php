<?php

namespace App\Tests\Functional;

class HomepageTest extends AbstractFunctionalTestCase
{
    public function testHomepageRespondsAndShowsSiteName(): void
    {
        $client = $this->createClientWithDatabase();
        $siteName = $_ENV['SITE_NAME'] ?? 'Mon association';
        $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('title', $siteName);
    }

    public function testHomepageListsArticles(): void
    {
        $client = $this->createClientWithDatabase();

        $crawler = $client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('h2');
    }
}
