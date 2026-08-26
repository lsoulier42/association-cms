<?php

namespace App\Tests\Functional;

use App\Entity\Article;
use App\Entity\SpecialPage;
use Doctrine\ORM\EntityManagerInterface;

class PublicPagesTest extends AbstractFunctionalTestCase
{
    public function testArticleIndex(): void
    {
        $client = $this->createClientWithDatabase();

        $client->request('GET', '/articles');

        self::assertResponseIsSuccessful();
    }

    public function testArticleShow(): void
    {
        $client = $this->createClientWithDatabase();
        $article = $this->findOneBy(Article::class);

        self::assertNotNull($article, 'Run the fixtures before the test suite.');

        $client->request('GET', sprintf('/articles/%d', (int) $article->getId()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', (string) $article->getTitle());
    }

    public function testSpecialContentPage(): void
    {
        $client = $this->createClientWithDatabase();
        $page = $this->findOneBy(SpecialPage::class, ['identifier' => 'content']);

        self::assertNotNull($page, 'Run the fixtures before the test suite.');

        $client->request('GET', sprintf('/page/%s', $page->getSlug()));

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', $page->getTitle());
    }

    public function testSpecialTeamPage(): void
    {
        $client = $this->createClientWithDatabase();
        $page = $this->findOneBy(SpecialPage::class, ['identifier' => 'team']);

        self::assertNotNull($page, 'Run the fixtures before the test suite.');

        $client->request('GET', sprintf('/page/%s', $page->getSlug()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.team-member-card');
    }

    public function testSpecialContactPage(): void
    {
        $client = $this->createClientWithDatabase();
        $page = $this->findOneBy(SpecialPage::class, ['identifier' => 'contact']);

        self::assertNotNull($page, 'Run the fixtures before the test suite.');

        $client->request('GET', sprintf('/page/%s', $page->getSlug()));

        self::assertResponseIsSuccessful();
        self::assertSelectorExists('.contact-info');
    }

    public function testUnknownPageReturns404(): void
    {
        $client = $this->createClientWithDatabase();

        $client->request('GET', '/page/does-not-exist');

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @template T of object
     *
     * @param class-string<T> $class
     * @param array<string, mixed> $criteria
     *
     * @return T|null
     */
    private function findOneBy(string $class, array $criteria = []): ?object
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        \assert($em instanceof EntityManagerInterface);

        return $em->getRepository($class)->findOneBy($criteria);
    }
}
