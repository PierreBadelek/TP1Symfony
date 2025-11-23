<?php

namespace App\Entity;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Ouvrage1Type extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('editeur')
            ->add('ISBN')
            ->add('annee')
            ->add('resume')
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categorieNom',
                'multiple' => true,
            ])
            ->add('auteurs', EntityType::class, [
                'class' => Auteur::class,
                'choice_label' => function (Auteur $auteur) {
                    return $auteur->getNom() . ' ' . $auteur->getPrenom();
                },'multiple' => true,
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
