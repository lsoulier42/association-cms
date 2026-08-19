<?php

namespace App\Controller\Admin;

use App\Entity\BoardMember;
use App\Enum\BoardMemberCategory;
use App\Enum\BoardMemberPrefix;
use App\Enum\BoardMemberTitle;
use App\Enum\BoardMemberDon;
use App\Enum\BoardMemberComite;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;

/**
 * @extends AbstractCrudController<BoardMember>
 */
class BoardMemberCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return BoardMember::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Membre du Bureau/CA')
            ->setEntityLabelInPlural('Membres du Bureau/CA')
            ->setDefaultSort(['category' => 'ASC', 'sortOrder' => 'ASC']);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')->hideOnForm();
        
        yield ChoiceField::new('prefix', 'Préfixe')
            ->setFormTypeOption('choices', BoardMemberPrefix::getChoices())
            ->formatValue(fn ($value) => $value?->getLabel())
            ->setRequired(false);
            
        yield TextField::new('firstName', 'Prénom');
        yield TextField::new('lastName', 'Nom');
        
        yield ChoiceField::new('category', 'Catégorie')
            ->setFormTypeOption('choices', BoardMemberCategory::getChoices())
            ->formatValue(fn ($value) => $value?->getLabel());
            
        yield ChoiceField::new('title', 'Titre')
            ->setFormTypeOption('choices', BoardMemberTitle::getGroupedChoices())
            ->formatValue(fn ($value) => $value?->getLabel())
            ->setRequired(false)
            ->setHelp('Le titre doit correspondre à la catégorie sélectionnée (obligatoire pour la plupart, optionnel pour Vice-présidents et Administrateurs).');
            
        yield ChoiceField::new('dons', 'Dons')
            ->setFormTypeOption('choices', BoardMemberDon::getChoices())
            ->allowMultipleChoices()
            ->setRequired(false)
            ->setHelp('Sélectionnez les dons associés à ce membre (optionnel).');

        yield ChoiceField::new('comites', 'Comités')
            ->setFormTypeOption('choices', BoardMemberComite::getChoices())
            ->allowMultipleChoices()
            ->setRequired(false)
            ->setHelp('Sélectionnez les comités auxquels ce membre appartient (optionnel).');

        yield TextField::new('expertise', 'Domaine d\'expertise')
            ->setRequired(false);
            
        yield TextField::new('qualifications', 'Qualifications')
            ->setRequired(false);
            
        yield AssociationField::new('photo', 'Photo')
            ->setRequired(false);
            
        yield IntegerField::new('sortOrder', 'Ordre de tri')
            ->setHelp('L\'ordre d\'affichage au sein de la catégorie (ex: 0, 1, 2...)');
    }
}
