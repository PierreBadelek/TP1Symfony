<?php

namespace App\Form;

use App\Entity\Exemplaire;
use App\Entity\Ouvrage;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExemplaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote', null, [
                'label' => 'Cote',
                'attr' => ['class' => 'form-control']
            ])
            ->add('etat', null, [
                'label' => 'État',
                'attr' => ['class' => 'form-control']
            ])
            ->add('disponible', null, [
                'label' => 'Disponible',
                'required' => false
            ])
            ->add('Ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
                'label' => 'Ouvrage',
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaire::class,
        ]);
    }
}
