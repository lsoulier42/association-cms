<?php

namespace App\Controller\Admin;

use App\Entity\Partner;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\UrlField;

/**
 * @extends AbstractCrudController<Partner>
 */
class PartnerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Partner::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre (initiales)'),
            TextField::new('subtitle', 'Sous-titre (titre complet)'),
            UrlField::new('logo', 'URL du logo'),
            UrlField::new('websiteUrl', 'Site web'),
            TextEditorField::new('description', 'Description'),
            BooleanField::new('isActive', 'Actif'),
            IntegerField::new('position', 'Position'),
        ];
    }
}
