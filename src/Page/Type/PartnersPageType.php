<?php

namespace App\Page\Type;

use App\Entity\SpecialPage;
use App\Page\AbstractPageType;
use App\Repository\PartnerRepository;

class PartnersPageType extends AbstractPageType
{
    public function __construct(
        private readonly PartnerRepository $partnerRepository,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'partner';
    }

    public function getLabel(): string
    {
        return 'Partenaires';
    }

    public function getTemplate(): string
    {
        return 'special_page/type/partner.html.twig';
    }

    public function getData(SpecialPage $page): array
    {
        return [
            'partners' => $this->partnerRepository->findActiveOrdered(),
        ];
    }
}
