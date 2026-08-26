<?php

namespace App\Page\Type;

use App\Entity\SpecialPage;
use App\Page\AbstractPageType;
use App\Repository\TeamMemberRepository;

class TeamPageType extends AbstractPageType
{
    public function __construct(
        private readonly TeamMemberRepository $teamMemberRepository,
    ) {
    }

    public function getIdentifier(): string
    {
        return 'team';
    }

    public function getLabel(): string
    {
        return 'Équipe';
    }

    public function getTemplate(): string
    {
        return 'special_page/type/team.html.twig';
    }

    public function getData(SpecialPage $page): array
    {
        return [
            'teamMembers' => $this->teamMemberRepository->findBy([], ['sortOrder' => 'ASC']),
        ];
    }
}
