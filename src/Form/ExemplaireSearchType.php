<?php

namespace App\Form;

use App\Entity\Exemplaire;
use App\Entity\Ouvrage;
use App\Enum\EtatExemplaire;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ExemplaireSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cote', TextType::class, ['required' => false])
            ->add('etat', ChoiceType::class, [
                'choices' => [
                    'Neuf' => EtatExemplaire::NEW,
                    'Correct' => EtatExemplaire::CORRECT,
                    'Endommagé' => EtatExemplaire::DAMAGED,
                ],
                'required' => false,
                'placeholder' => 'Tous les états',
            ])
            ->add('disponible', ChoiceType::class, [
                'choices' => [
                    'Disponible' => true,
                    'Indisponible' => false,
                ],
                'required' => false,
                'placeholder' => 'Peu importe',
            ])
            ->add('Ouvrage', EntityType::class, [
                'class' => Ouvrage::class,
                'choice_label' => 'titre',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Exemplaire::class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix(): string{
        return '';
    }


}
