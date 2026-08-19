<?php

namespace App\Controller;

use App\Repository\SpecialPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class PressMentionController extends AbstractController
{
    #[Route('/presse', name: 'press_mention_index')]
    public function index(SpecialPageRepository $specialPageRepository): Response
    {
        $specialPage = $specialPageRepository->findOneBy(['identifier' => 'press']);

        if ($specialPage) {
            return $this->redirectToRoute('special_page_show', ['slug' => $specialPage->getSlug()]);
        }

        // Fallback if special page doesn't exist yet
        return $this->render('press_mention/index.html.twig', [
            'mentions' => [],
        ]);
    }
}
