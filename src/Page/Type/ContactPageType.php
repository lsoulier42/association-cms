<?php

namespace App\Page\Type;

use App\Entity\SpecialPage;
use App\Page\AbstractPageType;
use App\Repository\AssociationRepository;

class ContactPageType extends AbstractPageType
{
    public function __construct(
        private readonly AssociationRepository $associationRepository,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'contact';
    }

    public function getLabel(): string
    {
        return 'Contact';
    }

    public function getTemplate(): string
    {
        return 'special_page/type/contact.html.twig';
    }

    public function getData(SpecialPage $page): array
    {
        return [
            'association' => $this->associationRepository->findOneBy([]),
        ];
    }
}
