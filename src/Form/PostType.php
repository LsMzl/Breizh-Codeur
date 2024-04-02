<?php

namespace App\Form;

use App\Entity\Posts;
use App\Entity\Tags;
use App\Entity\Users;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextareaType::class,
            [
                'label'=>'Titre'
            ])
            ->add('slug', TextareaType::class,
            [
                'label'=>'Slug'
            ])
            ->add('summary', TextareaType::class,
            [
                'label'=>'Résumé'
            ])
            ->add('content', TextareaType::class,
            [
                'label'=>'Contenu'
            ])
            // ->add('published_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('updated_at', null, [
            //     'widget' => 'single_text',
            // ])
            // ->add('author', EntityType::class, [
            //     'class' => Users::class,
            //     'choice_label' => 'id',
            // ])
            // ->add('tags', EntityType::class, [
            //     'class' => Tags::class,
            //     'choice_label' => 'id',
            //     'multiple' => true,
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
