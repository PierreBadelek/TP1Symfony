<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Ouvrage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ouvrage1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', null, [
                'label' => 'Titre',
                'attr' => ['class' => 'form-control']
            ])
            ->add('editeur', null, [
                'label' => 'Éditeur',
                'attr' => ['class' => 'form-control']
            ])
            ->add('isbn', null, [
                'label' => 'ISBN',
                'attr' => ['class' => 'form-control']
            ])
            ->add('annee', null, [
                'label' => 'Année',
                'attr' => ['class' => 'form-control']
            ])
            ->add('resume', null, [
                'label' => 'Résumé',
                'attr' => ['class' => 'form-control', 'rows' => 5]
            ])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categorieNom',
                'label' => 'Catégories',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
            ->add('auteurs', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => function(Auteur $auteur) {
                    return $auteur->getNom() . ' ' . $auteur->getPrenom();
                },
                'label' => 'Auteurs',
                'multiple' => true,
                'expanded' => true,
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ouvrage::class,
        ]);
    }
}
