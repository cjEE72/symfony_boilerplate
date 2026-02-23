<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['data'] ?? null;

        $builder
            ->add('email')
            ->add('roles', ChoiceType::class, [
        'choices'  => [
            'Utilisateur' => 'ROLE_USER',
            'Manager' => 'ROLE_MANAGER',
            'Administrateur' => 'ROLE_ADMIN',
            ],
            'label' => 'Rôle principal',
            'required' => true,
            'multiple' => false, // On en choisit un seul dans la liste
            'expanded' => false, // false = liste déroulante (combo)
            ])
            ->add('firstname')
            ->add('lastname')
        ;
        // ON AJOUTE LE MOT DE PASSE UNIQUEMENT SI C'EST UN NOUVEAU
        if (!$user || null === $user->getId()) {
            $builder->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
            ]);
        }
        
        $builder->get('roles')
        ->addModelTransformer(new CallbackTransformer(
        function ($rolesArray) { return $rolesArray[0] ?? null; }, // Array vers String (pour l'affichage)
        function ($rolesString) { return [$rolesString]; }        // String vers Array (pour la base)
    ));
    }
    
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
