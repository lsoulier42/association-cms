<?php

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class CategoryController extends AbstractController
{
    #[Route('/categories/{slug}', name: 'category_show')]
    public function show(string $slug, CategoryRepository $repository): Response
    {
        $category = $repository->findOneBy(['slug' => $slug]);
        if ($category === null) {
            throw $this->createNotFoundException('Catégorie introuvable.');
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'articles' => $category->getArticles(),
        ]);
    }
}
