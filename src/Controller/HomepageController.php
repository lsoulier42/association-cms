<?php

namespace App\Controller;

use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted("PUBLIC_ACCESS")]
#[Route(path: "/")]
class HomepageController extends AbstractController
{
    /**
     * @return Response
     */
    #[Route(path: '/', name: 'homepage')]
    public function index(ArticleRepository $articleRepository): Response
    {
        return $this->render('homepage/index.html.twig', [
            'last_articles' => $articleRepository->findBy([], ['publishedAt' => 'DESC'], 4),
        ]);
    }
}
