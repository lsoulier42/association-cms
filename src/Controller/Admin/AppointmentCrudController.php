<?php

namespace App\Controller\Admin;

use App\Entity\Appointment;
use App\Entity\SpecialPage;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Shuchkin\SimpleXLSX;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @extends AbstractCrudController<Appointment>
 */
class AppointmentCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Appointment::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Rendez-vous')
            ->setEntityLabelInPlural('Rendez-vous')
            ->setPageTitle(Crud::PAGE_INDEX, 'Rendez-vous')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un rendez-vous')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le rendez-vous')
            ->setDefaultSort(['date' => 'DESC']);
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
            DateTimeField::new('date', 'Date')->setFormat('dd/MM/yyyy HH:mm'),
            TextField::new('location', 'Lieu'),
            TextareaField::new('subject', 'Sujet'),
        ];
    }

    #[AdminRoute(path: '/import-excel', name: 'import_appointment_excel')]
    public function importExcel(AdminContext $context, Request $request, EntityManagerInterface $em, AdminUrlGenerator $adminUrlGenerator): Response
    {
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
                // Find "RDV" sheet index
                $sheetIndex = -1;
                $sheetNames = $xlsx->sheetNames();
                foreach ($sheetNames as $idx => $name) {
                    if (strcasecmp(trim($name), 'RDV') === 0 || strcasecmp(trim($name), 'Rendez-vous') === 0) {
                        $sheetIndex = $idx;
                        break;
                    }
                }

                if ($sheetIndex === -1) {
                    // Fallback to first sheet or try to find columns
                    $sheetIndex = 0;
                }

                $rows = $xlsx->rows($sheetIndex);
                $specialPage = $em->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'appointments']);
                if (!$specialPage) {
                    // Try 'rdv' if 'appointments' is not found
                    $specialPage = $em->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'rdv']);
                }

                foreach ($rows as $index => $row) {
                    if ($index === 0) continue; // Ignore l'en-tête
                    
                    if (count($row) < 3) continue;
                    
                    [$dateStr, $location, $subject] = $row;
                    
                    if (empty($dateStr) && empty($location) && empty($subject)) continue;
                    if ($dateStr === 'DATE' && $location === 'RDV') continue; // Si en-tête mal ignoré
                    
                    $date = null;
                    if ($dateStr) {
                        // Excel dates might be parsed as strings like '18.06.2025' or '18/06/2025' or standard Excel float values
                        if (is_numeric($dateStr)) {
                            // Convert Excel date to PHP DateTime
                            $unixDate = ($dateStr - 25569) * 86400;
                            $date = new \DateTimeImmutable('@' . $unixDate);
                        } else {
                            // Try multiple formats
                            $date = \DateTimeImmutable::createFromFormat('Y.m.d', $dateStr);
                            if (!$date) $date = \DateTimeImmutable::createFromFormat('Y-m-d', $dateStr);
                            if (!$date) $date = \DateTimeImmutable::createFromFormat('d.m.Y', $dateStr);
                            if (!$date) $date = \DateTimeImmutable::createFromFormat('d/m/Y', $dateStr);
                        }
                    }

                    if (!$date) {
                         // Default to current date if missing or unparseable, or skip?
                         // It's better to skip if date is completely unparseable for a rendezvous
                         if (!empty($dateStr)) {
                             // Try natural parsing
                             try {
                                 $parsed = new \DateTimeImmutable($dateStr);
                                 $date = $parsed;
                             } catch (\Exception $e) {
                                 // Ignore
                             }
                         }
                         if (!$date) continue;
                    }

                    // Vérifier si le RDV existe déjà pour éviter les doublons (Même date, lieu, sujet)
                    $existing = $em->getRepository(Appointment::class)->findOneBy([
                        'date' => $date,
                        'location' => $location,
                        'subject' => $subject
                    ]);
                    
                    if ($existing) continue;

                    $appointment = new Appointment();
                    $appointment->setDate($date);
                    $appointment->setLocation($location);
                    $appointment->setSubject($subject);
                    
                    if ($specialPage) {
                        $appointment->setSpecialPage($specialPage);
                    }
                    
                    $em->persist($appointment);
                }
                
                $em->flush();
                $this->addFlash('success', 'Importation Excel réussie.');
            } else {
                $this->addFlash('danger', 'Erreur de lecture du fichier : ' . SimpleXLSX::parseError());
            }

            return $this->redirect($adminUrlGenerator->setAction(Action::INDEX)->generateUrl());
        }

        return $this->render('admin/import_appointment_excel.html.twig', [
            'form' => $form->createView(),
            'page_title' => 'Importer des rendez-vous (Excel)',
        ]);
    }

    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        if (!$entityInstance instanceof Appointment) {
            return;
        }

        if (!$entityInstance->getSpecialPage()) {
            $specialPage = $entityManager->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'appointments']);
            if (!$specialPage) {
                $specialPage = $entityManager->getRepository(SpecialPage::class)->findOneBy(['identifier' => 'rdv']);
            }
            if ($specialPage) {
                $entityInstance->setSpecialPage($specialPage);
            }
        }
        
        parent::persistEntity($entityManager, $entityInstance);
    }
}
