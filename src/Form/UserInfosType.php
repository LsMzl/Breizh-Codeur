<?php

namespace App\Form;

use App\Entity\Users;
use Doctrine\DBAL\Types\TextType as TypesTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserInfosType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'full_name',
                TextType::class,
                [
                    'label' => 'Nom complet',
                    'required'=> false
                ]
            )
            ->add(
                'username',
                TextType::class,
                [
                    'label' => 'Pseudo',
                    'required'=> false
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => 'Email',
                    'required'=> false
                ]
            )
            ->add(
                'password',
                PasswordType::class,
                [
                    'label' => 'Mot de passe actuel',
                    'required'=> false,
                    'disabled' => true
                ]
            )
            ->add(
                'picture',
                FileType::class,
                [
                    'label' => 'Photo de profil',
                    'required'=> false,
                    'mapped' => false,
                    'constraints' => [
                        new Image()
                    ]
                ]
            )
            // ->add(
            //     'roles', 
            //     ChoiceType::class,
            //     [
            //         'label' => 'Rôle',
            //         'required'=> false,
            //         'choices' => [
            //             'Rôles utilisateur' => [
            //             'ROLE_ADMIN' => true,
            //             'ROLE_USER' => true
            //             ]
            //         ]
            //     ]
            // )
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}