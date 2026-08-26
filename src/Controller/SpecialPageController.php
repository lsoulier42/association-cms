<?php

namespace App\Controller;

use App\Page\PageTypeRegistry;
use App\Repository\SpecialPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class SpecialPageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'special_page_show')]
    public function show(
        string $slug,
        SpecialPageRepository $specialPageRepository,
        PageTypeRegistry $pageTypeRegistry,
    ): Response {
        $specialPage = $specialPageRepository->findOneBy(['slug' => $slug]);

        if (!$specialPage) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        $pageType = $pageTypeRegistry->get($specialPage->getIdentifier());

        if ($pageType === null) {
            throw $this->createNotFoundException(sprintf('Type de page inconnu : "%s"', $specialPage->getIdentifier()));
        }

        return $this->render($pageType->getTemplate(), [
            'specialPage' => $specialPage,
            ...$pageType->getData($specialPage),
        ]);
    }
}
