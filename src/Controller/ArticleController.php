<?php

namespace App\Controller;

use App\Entity\Article;
use App\Repository\ArticleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class ArticleController extends AbstractController
{
    #[Route('/articles', name: 'article_index')]
    public function index(ArticleRepository $repository): Response
    {
        return $this->render('article/index.html.twig', [
            'articles' => $repository->findBy([], ['publishedAt' => 'DESC']),
        ]);
    }

    #[Route('/articles/{id}', name: 'article_show', requirements: ['id' => '\d+'])]
    public function show(Article $article): Response
    {
        return $this->render('article/show.html.twig', [
            'article' => $article,
        ]);
    }
}
