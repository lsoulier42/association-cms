<?php

namespace App\Tests\Page;

use App\Entity\SpecialPage;
use App\Page\AbstractPageType;
use App\Page\PageTypeRegistry;
use App\Page\Type\ContactPageType;
use App\Page\Type\ContentType;
use PHPUnit\Framework\TestCase;

class PageTypeRegistryTest extends TestCase
{
    public function testRegistersTypesByIdentifier(): void
    {
        $registry = new PageTypeRegistry([new ContentType()]);

        self::assertTrue($registry->has('content'));
        self::assertInstanceOf(ContentType::class, $registry->get('content'));
        self::assertNull($registry->get('unknown'));
        self::assertFalse($registry->has('unknown'));
    }

    public function testGetChoicesMapsLabelsToIdentifiers(): void
    {
        $registry = new PageTypeRegistry([new ContentType()]);

        self::assertSame(['Contenu simple' => 'content'], $registry->getChoices());
    }

    public function testLastRegisteredTypeWins(): void
    {
        $override = new class extends AbstractPageType {
            public function getIdentifier(): string
            {
                return 'content';
            }

            public function getLabel(): string
            {
                return 'Surcharge';
            }
        };

        $registry = new PageTypeRegistry([new ContentType(), $override]);

        self::assertSame('Surcharge', $registry->get('content')?->getLabel());
    }

    public function testAbstractPageTypeDefaults(): void
    {
        $type = new ContentType();
        $page = new SpecialPage();

        self::assertSame('special_page/show.html.twig', $type->getTemplate());
        self::assertSame([], $type->getData($page));
        self::assertNotSame('', $type->getLabel());
        self::assertInstanceOf(ContactPageType::class, new ContactPageType(
            $this->createStub(\App\Repository\AssociationRepository::class)
        ));
    }
}
