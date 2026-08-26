<?php

namespace App\Controller\Admin;

use App\Entity\Media;
use App\Entity\PressMention;
use App\Entity\SpecialPage;
use App\Service\MediaMetadataScraper;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Shuchkin\SimpleXLSX;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends AbstractCrudController<PressMention>
 */
class PressMentionCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly MediaMetadataScraper $scraper
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return PressMention::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Mention presse')
            ->setEntityLabelInPlural('Mentions presse')
            ->setPageTitle(Crud::PAGE_INDEX, 'Mentions presse')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une mention presse')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier la mention presse');
    }

    public function configureActions(Actions $actions): Actions
    {
        $importAction = Action::new('importExcel', 'Importer Excel', 'fa fa-file-excel')
            ->linkToCrudAction('importExcel')
            ->createAsGlobalAction();

        return $actions->add(Crud::PAGE_INDEX, $importAction);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            UrlField::new('externalLink', 'Lien de l\'article'),
            ChoiceField::new('type', 'Type')->setChoices(PressMention::TYPES)->renderAsBadges([
                PressMention::TYPE_ARTICLE => 'primary',
                PressMention::TYPE_TRIBUNE => 'info',
                PressMention::TYPE_DEPECHE => 'secondary',
                PressMention::TYPE_INTERVIEW => 'success',
                PressMention::TYPE_ANNONCE => 'warning',
            ]),
            TextField::new('title', 'Titre (auto-rempli si vide)')->setRequired(false),
            AssociationField::new('media', 'Média (auto-rempli si vide)'),
            DateTimeField::new('publishedAt', 'Date de publication (auto-rempli si vide)'),
        ];
    }

    /**
     * @param AdminContext<PressMention> $context
     */
    #[AdminRoute(path: '/import-excel', name: 'import_excel')]
    public function importExcel(
        AdminContext $context,
        Request $request,
        EntityManagerInterface $em,
        AdminUrlGenerator $adminUrlGenerator
    ): Response {
        $form = $this->createFormBuilder()
            ->add('file', FileType::class, [
                'label' => 'Fichier Excel (.xlsx)',
                'attr' => ['accept' => '.xlsx']
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('file')->getData();

            if ($xlsx = SimpleXLSX::parse($file->getPathname())) {
                $rows = $xlsx->rows();
                $specialPage = $em->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'press']);
                $mediaRepo = $em->getRepository(Media::class);

                foreach ($rows as $index => $row) {
                    if ($index === 0) {
                        continue; // Ignore l'en-tête
                    }

                    if (count($row) < 5) {
                        continue;
                    }

                    [$dateStr, $journalName, $typeStr, $title, $link] = $row;

                    if (empty($link) || empty($title)) {
                        continue;
                    }
                    if ($link === 'LIEN') {
                        continue; // Si en-tête mal ignoré
                    }

                    // Vérifier si le lien existe déjà pour éviter les doublons
                    $existing = $em->getRepository(PressMention::class)->findOneBy(['externalLink' => $link]);
                    if ($existing) {
                        continue;
                    }

                    $mention = new PressMention();
                    $mention->setExternalLink($link);
                    $mention->setTitle($title);

                    $matchedType = PressMention::TYPE_ARTICLE;
                    foreach (PressMention::TYPES as $validType) {
                        if (strcasecmp($validType, $typeStr) === 0) {
                            $matchedType = $validType;
                            break;
                        }
                    }
                    $mention->setType($matchedType);

                    $scrapedData = $this->scraper->scrape($link);

                    if ($dateStr) {
                        $date = \DateTimeImmutable::createFromFormat('Y.m.d', $dateStr);
                        if ($date) {
                            $mention->setPublishedAt($date);
                        } else {
                            // Essayer d'autres formats si besoin, par exemple Y-m-d
                            $dateAlternative = \DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
                            if ($dateAlternative) {
                                $mention->setPublishedAt($dateAlternative);
                            }
                        }
                    } elseif ($scrapedData['publishedAt']) {
                        $mention->setPublishedAt($scrapedData['publishedAt']);
                    }

                    if ($journalName) {
                        $media = $mediaRepo->findOneBy(['name' => $journalName]);

                        if (!$media && $scrapedData['media']) {
                            $media = $scrapedData['media'];
                            $media->setName($journalName); // Force le nom Excel
                        } elseif (!$media) {
                            $media = new Media();
                            $media->setName($journalName);
                            $media->setLogo($scrapedData['mediaLogoUrl'] ?? null);
                            $parsedUrl = parse_url($link);
                            if (isset($parsedUrl['host'])) {
                                $media->setWebsiteUrl(($parsedUrl['scheme'] ?? 'https') . '://' . $parsedUrl['host']);
                            }
                            $em->persist($media);
                        } else {
                            // Mettre à jour le logo/url si manquant
                            if (!$media->getLogo() && !empty($scrapedData['mediaLogoUrl'])) {
                                $media->setLogo($scrapedData['mediaLogoUrl']);
                            }
                            if (!$media->getWebsiteUrl()) {
                                $parsedUrl = parse_url($link);
                                if (isset($parsedUrl['host'])) {
                                    $media->setWebsiteUrl(
                                        ($parsedUrl['scheme'] ?? 'https') . '://' . $parsedUrl['host']
                                    );
                                }
                            }
                        }
                        $em->flush();
                        $mention->setMedia($media);
                    }

                    if ($specialPage) {
                        $mention->setSpecialPage($specialPage);
                    }

                    $em->persist($mention);
                }

                $em->flush();
                $this->addFlash('success', 'Importation Excel réussie.');
            } else {
                $this->addFlash('danger', 'Erreur de lecture du fichier : ' . SimpleXLSX::parseError());
            }

            return $this->redirect($adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        return $this->render('admin/import_excel.html.twig', [
            'form' => $form->createView(),
            'page_title' => 'Importer des mentions presse (Excel)',
        ]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->autoFill($entityInstance, $entityManager);
        parent::persistEntity($entityManager, $entityInstance);
    }

    public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        $this->autoFill($entityInstance, $entityManager);
        parent::updateEntity($entityManager, $entityInstance);
    }

    private function autoFill(PressMention $pressMention, EntityManagerInterface $entityManager): void
    {
        if (!$pressMention->getExternalLink()) {
            return;
        }

        if (!$pressMention->getSpecialPage()) {
            $specialPage = $entityManager->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'press']);
            if ($specialPage) {
                $pressMention->setSpecialPage($specialPage);
            }
        }

        if (!$pressMention->getTitle() || !$pressMention->getMedia() || !$pressMention->getPublishedAt()) {
            $data = $this->scraper->scrape($pressMention->getExternalLink());

            if (!$pressMention->getTitle() && $data['title']) {
                $pressMention->setTitle($data['title']);
            }

            if (!$pressMention->getTitle()) {
                $pressMention->setTitle('Article de presse du ' . date('d/m/Y'));
            }

            if (!$pressMention->getMedia() && $data['media']) {
                $pressMention->setMedia($data['media']);
            }

            if (!$pressMention->getPublishedAt() && $data['publishedAt']) {
                $pressMention->setPublishedAt($data['publishedAt']);
            }
        }
    }
}
