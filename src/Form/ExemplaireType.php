<?php

namespace App\Form;

use App\Entity\Exemplaire;
use App\Entity\Ouvrage;
use App\Enum\EtatExemplaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
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
            ->add('etat', EnumType::class, [
                'class' => EtatExemplaire::class,
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
