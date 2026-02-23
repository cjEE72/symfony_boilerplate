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
        $builder->add('confirmHighPrice', CheckboxType::class, [
            'label' => 'Je confirme la mise en vente de ce produit à prix élevé',
            'mapped' => false,
            'constraints' => [new IsTrue(['message' => 'Vous devez confirmer le prix.'])]
        ]);
    }
}