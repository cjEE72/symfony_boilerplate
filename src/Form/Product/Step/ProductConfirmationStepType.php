<?php

namespace App\Form\Product\Step;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;

class ProductConfirmationStepType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('confirm', CheckboxType::class, [
            'label' => 'Je confirme que ce produit a un prix supérieur à 500€',
            'mapped' => false, // Ce champ n'est pas dans l'entité
            'constraints' => [
                new IsTrue(['message' => 'Vous devez cocher la case pour continuer.']),
            ],
        ]);
    }
}