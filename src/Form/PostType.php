<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class,
            [
                'label'=>'Titre'
            ])
            ->add('slug', TextType::class,
            [
                'label'=>'Slug',
                'required'=> false
            ])
            ->add('summary', TextType::class,
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
