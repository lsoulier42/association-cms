<?php

namespace App\Controller\Admin;

use App\Entity\Association;
use App\Entity\User;
use App\Controller\Admin\PartnerCrudController;
use App\Form\AssociationType;
use App\Repository\AssociationRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AssociationRepository $associationRepository
    ) {
    }

    public function index(): Response
    {
        $association = $this->associationRepository->getSettings() ?? new Association();
        
        $form = $this->createForm(AssociationType::class, $association);
        $form->handleRequest($this->container->get('request_stack')->getCurrentRequest());

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($association);
            $this->entityManager->flush();

            $this->addFlash('success', 'Paramètres de l\'association mis à jour.');

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/dashboard.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Association Admin');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retour au site', 'fa fa-undo', 'homepage');

        yield MenuItem::section('Contenus');
        yield MenuItem::linkTo(CategoryCrudController::class, 'Menus', 'fa fa-bars');
        yield MenuItem::linkTo(ArticleCrudController::class, 'Articles', 'fa fa-newspaper');
        yield MenuItem::linkTo(PressMentionCrudController::class, 'Mentions presse', 'fa fa-bullhorn');
        yield MenuItem::linkTo(PartnerCrudController::class, 'Partenaires', 'fa fa-handshake');
        yield MenuItem::linkTo(AppointmentCrudController::class, 'Rendez-vous', 'fa fa-calendar-check');
        yield MenuItem::linkTo(LinkedInPostCrudController::class, 'Posts LinkedIn', 'fa fa-linkedin');

        yield MenuItem::section('Administration');
        yield MenuItem::linkToDashboard('Contact', 'fa fa-address-book');
        yield MenuItem::linkTo(SpecialPageCrudController::class, 'Pages spéciales', 'fa fa-file-lines');
        yield MenuItem::linkTo(BoardMemberCrudController::class, 'Bureau / CA', 'fa fa-users-viewfinder');
        yield MenuItem::linkTo(MediaCrudController::class, 'Médias', 'fa fa-building');
        yield MenuItem::linkTo(UserCrudController::class, 'Utilisateurs', 'fa fa-users');
    }
}
