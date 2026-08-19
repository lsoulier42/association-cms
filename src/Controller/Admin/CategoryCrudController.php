<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<Category>
 */
class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')
            ->setPageTitle(Crud::PAGE_INDEX, 'Menus')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter un menu')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier le menu')
            ->setDefaultSort(['menuOrder' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            SlugField::new('slug', 'Slug')->setTargetFieldName('name'),
            IntegerField::new('menuOrder', 'Ordre du menu'),
        ];
    }
}
