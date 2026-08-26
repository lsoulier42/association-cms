<?php

namespace App\Twig;

use App\Repository\AssociationRepository;
use App\Service\MenuProvider;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class MenuExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly MenuProvider $menuProvider,
        private readonly AssociationRepository $associationRepository,
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'menu_categories' => $this->menuProvider->getMenuCategories(),
            'association_settings' => $this->associationRepository->getSettings(),
        ];
    }
}
