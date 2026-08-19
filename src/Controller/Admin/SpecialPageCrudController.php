<?php

namespace App\Controller\Admin;

use App\Entity\SpecialPage;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<SpecialPage>
 */
class SpecialPageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SpecialPage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('title', 'Titre'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('title'),
            TextField::new('identifier', 'Identifiant système'),
            AssociationField::new('category', 'Catégorie'),
            TextEditorField::new('content', 'Contenu'),
            BooleanField::new('showInMenu', 'Dans le menu ?'),
            IntegerField::new('menuOrder', 'Ordre du menu'),
        ];
    }
}
