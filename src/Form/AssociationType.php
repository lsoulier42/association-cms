<?php

namespace App\Form;

use App\Entity\Association;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<Association>
 */
class AssociationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('address', TextType::class, [
                'label' => 'Siège Social',
                'attr' => ['placeholder' => '10, rue du Dr Heydenreich - 54000 Nancy']
            ])
            ->add('contactEmail', EmailType::class, [
                'label' => 'Email de Contact',
            ])
            ->add('phoneNumber', TelType::class, [
                'label' => 'Numéro de Téléphone',
                'required' => false,
            ])
            ->add('linkedinLink', UrlType::class, [
                'label' => 'Lien LinkedIn',
                'required' => false,
            ])
            ->add('instagramLink', UrlType::class, [
                'label' => 'Lien Instagram',
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Association::class,
        ]);
    }
}
