<?php

namespace App\Controller\Admin;

use App\Entity\LinkedInPost;
use App\Service\LinkedInApiService;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;

/**
 * @extends AbstractCrudController<LinkedInPost>
 */
class LinkedInPostCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return LinkedInPost::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $importAction = Action::new('importLinkedIn', 'Importer depuis LinkedIn', 'fa fa-download')
            ->linkToCrudAction('importLinkedInAction')
            ->createAsGlobalAction()
            ->setCssClass('btn btn-success');

        return $actions
            ->add(Crud::PAGE_INDEX, $importAction)
            ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            UrlField::new('embedLink', 'Lien d’intégration'),
        ];
    }

    /**
     * @param AdminContext<LinkedInPost> $context
     */
    #[AdminRoute(path: '/import-linkedin', name: 'import_linkedin_action')]
    public function importLinkedInAction(
        AdminContext $context,
        LinkedInApiService $linkedInApiService,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $result = $linkedInApiService->importPosts();

        if ($result['success']) {
            $this->addFlash('success', sprintf('Importation terminée : %d nouveaux posts récupérés.', $result['count']));
        } else {
            $this->addFlash('danger', $result['message']);
        }

        $targetUrl = $adminUrlGenerator
            ->setController(self::class)
            ->setAction(Crud::PAGE_INDEX)
            ->generateUrl();

        return $this->redirect($targetUrl);
    }
}
