<?php

namespace App\Form;

use App\Entity\Auteur;
use App\Entity\Categorie;
use App\Entity\Ouvrage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OuvrageSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, ['required' => false])
            ->add('ISBN', IntegerType::class, ['required' => false])
            ->add('annee', IntegerType::class, ['required' => false])
            ->add('categories', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'categorieNom',
                'multiple' => true,
                'required' => false,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ouvrage::class,
             'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string{
        return '';
    }
}
