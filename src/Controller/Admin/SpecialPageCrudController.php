<?php

namespace App\Controller\Admin;

use App\Entity\SpecialPage;
use App\Page\PageTypeRegistry;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<SpecialPage>
 */
class SpecialPageCrudController extends AbstractCrudController
{
    public function __construct(
        private readonly PageTypeRegistry $pageTypeRegistry,
    ) {
    }

    public static function getEntityFqcn(): string
    {
        return SpecialPage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        yield TextField::new('title', 'Titre');
        yield SlugField::new('slug', 'Slug')->setTargetFieldName('title');
        yield ChoiceField::new('identifier', 'Type de page')
            ->setChoices($this->pageTypeRegistry->getChoices())
            ->setHelp('Détermine le rendu et les données affichées par la page.');
        yield AssociationField::new('category', 'Catégorie');
        yield TextEditorField::new('content', 'Contenu');

        yield FormField::addFieldset('Menu');
        yield BooleanField::new('showInMenu', 'Dans le menu ?');
        yield IntegerField::new('menuOrder', 'Ordre du menu');
    }
}
