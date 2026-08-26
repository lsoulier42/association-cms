<?php

namespace App\Controller\Admin;

use App\Entity\TeamMember;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

/**
 * @extends AbstractCrudController<TeamMember>
 */
class TeamMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return TeamMember::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Membre de l\'équipe')
            ->setEntityLabelInPlural('Membres de l\'équipe')
            ->setDefaultSort(['sortOrder' => 'ASC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions->reorder(Action::INDEX, [Action::EDIT, Action::DELETE]);
    }

    public function configureFields(string $pageName): iterable
    {
        yield FormField::addColumn(6);
        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');
        yield TextField::new('role', 'Rôle')
            ->setHelp('Fonction au sein de l\'association (ex : Président.e, Trésorier.ère…).');
        yield EmailField::new('email', 'Email')
            ->setHelp('Optionnel : adresse de contact publique.');
        yield IntegerField::new('sortOrder', 'Ordre d\'affichage');

        yield FormField::addColumn(6);
        yield AssociationField::new('photo', 'Photo')
            ->setRequired(false)
            ->setHelp('Médias de la bibliothèque (voir section Médias).');
        yield TextareaField::new('bio', 'Biographie')
            ->hideOnIndex()
            ->setHelp('Courte présentation du membre (optionnelle).');
    }
}
