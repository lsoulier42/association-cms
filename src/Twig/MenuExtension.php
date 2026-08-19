<?php

namespace App\Twig;

use App\Service\MenuProvider;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;

class MenuExtension extends AbstractExtension implements GlobalsInterface
{
    public function __construct(
        private readonly MenuProvider $menuProvider,
        private readonly \Doctrine\ORM\EntityManagerInterface $entityManager
    ) {
    }

    public function getGlobals(): array
    {
        return [
            'menu_categories' => $this->menuProvider->getMenuCategories(),
            'association_settings' => $this->entityManager->getRepository(\App\Entity\Association::class)->getSettings(),
        ];
    }
}
