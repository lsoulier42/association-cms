<?php

namespace App\Controller;

use App\Repository\PressMentionRepository;
use App\Repository\SpecialPageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('PUBLIC_ACCESS')]
class SpecialPageController extends AbstractController
{
    #[Route('/page/{slug}', name: 'special_page_show')]
    public function show(
        string $slug,
        SpecialPageRepository $specialPageRepository,
        PressMentionRepository $pressMentionRepository,
        \App\Repository\AppointmentRepository $appointmentRepository,
        \App\Repository\PartnerRepository $partnerRepository,
        \App\Repository\AssociationRepository $associationRepository,
        \App\Repository\BoardMemberRepository $boardMemberRepository
    ): Response {
        $specialPage = $specialPageRepository->findOneBy(['slug' => $slug]);

        if (!$specialPage) {
            throw $this->createNotFoundException('Page non trouvée');
        }

        $data = [
            'specialPage' => $specialPage,
        ];

        // Logic specific to 'press' type
        if ($specialPage->getIdentifier() === 'press') {
            // We list all PressMentions, regardless of whether they are explicitly linked to this page
            // (But we could also filter by specialPage if we wanted several press pages)
            $data['pressMentions'] = $pressMentionRepository->findBy(
                [],
                ['publishedAt' => 'DESC', 'createdAt' => 'DESC']
            );
        }

        // Logic specific to 'appointments' type
        if ($specialPage->getIdentifier() === 'appointments') {
            $data['appointments'] = $appointmentRepository->findBy(
                [],
                ['date' => 'DESC']
            );
        }

        // Logic specific to 'partner' type
        if ($specialPage->getIdentifier() === 'partner') {
            $data['partners'] = $partnerRepository->findActiveOrdered();
        }

        // Logic specific to 'bureau' type
        if ($specialPage->getIdentifier() === 'bureau') {
            $data['boardMembers'] = $boardMemberRepository->findBy(
                ['category' => [
                    \App\Enum\BoardMemberCategory::BUREAU_RESTREINT,
                    \App\Enum\BoardMemberCategory::VICE_PRESIDENTS
                ]],
                ['sortOrder' => 'ASC']
            );
            return $this->render('special_page/bureau.html.twig', $data);
        }

        // Logic specific to 'conseil-administration' type
        if ($specialPage->getIdentifier() === 'conseil-administration') {
            $data['boardMembers'] = $boardMemberRepository->findBy(
                [],
                ['category' => 'ASC', 'sortOrder' => 'ASC']
            );
            return $this->render('special_page/conseil_administration.html.twig', $data);
        }

        // Logic specific to 'contact' type
        if ($specialPage->getIdentifier() === 'contact') {
            $data['association'] = $associationRepository->findOneBy([]);
            return $this->render('special_page/contact.html.twig', $data);
        }

        return $this->render('special_page/show.html.twig', $data);
    }
}
