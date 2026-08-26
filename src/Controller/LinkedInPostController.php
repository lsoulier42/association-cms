<?php

namespace App\Controller;

use App\Repository\LinkedInPostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class LinkedInPostController extends AbstractController
{
    #[Route('/linkedin', name: 'linkedin_post_index')]
    public function index(LinkedInPostRepository $repository): Response
    {
        return $this->render('linkedin_post/index.html.twig', [
            'posts' => $repository->findBy([], ['createdAt' => 'DESC']),
        ]);
    }
}
