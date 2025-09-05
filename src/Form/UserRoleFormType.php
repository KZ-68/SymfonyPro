<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UserRoleFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $permissions = [
            'User role' => 'ROLE_USER' ,
            'Moderator role' => 'ROLE_MODERATOR'
        ];

        $builder
         ->add('roles', ChoiceType::class, [
                'label'   => 'Choose the role',
                'choices' => $permissions,
                'attr' => [
                    'class' => 'form-single-select'
                ]
            ])
            ->add(
                'save', SubmitType::class, [
                    'attr' => [
                        'class' => 'btn btn-success'
                    ]
                ]
            );
            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
       $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}