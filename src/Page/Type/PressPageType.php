<?php

namespace App\Page\Type;

use App\Page\AbstractPageType;
use App\Repository\PressMentionRepository;

class PressPageType extends AbstractPageType
{
    public function __construct(
        private readonly PressMentionRepository $pressMentionRepository,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'press';
    }

    public function getLabel(): string
    {
        return 'Mentions presse';
    }

    public function getTemplate(): string
    {
        return 'special_page/type/press.html.twig';
    }

    public function getData(\App\Entity\SpecialPage $page): array
    {
        return [
            'pressMentions' => $this->pressMentionRepository->findBy(
                [],
                ['publishedAt' => 'DESC', 'createdAt' => 'DESC']
            ),
        ];
    }
}
