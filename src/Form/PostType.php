<?php

namespace App\Form;

use App\Entity\Posts;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\AsciiSlugger;

class PostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => 'Titre',
                    'required'=> false
                ]
            )
            // ->add('slug', TextType::class,
            // [
            //     'label'=>'Slug',
            //     'required'=> false
            // ])
            ->add(
                'summary',
                TextType::class,
                [
                    'label' => 'Résumé',
                    'required'=> false
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => 'Contenu',
                    'required'=> false
                ]
            );

        // Ajout d'un événement sur le submit du formulaire
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) 
        {
            /** @var Post $post */
            $post = $event->getData();

            // Si le champ slug est vide et si le champ title n'est pas vide
            if($post->getSlug() == null && $post->getTitle() !== null)
            {
                $slugger = new AsciiSlugger();
                // Génération du slug en récupérant et transformant le titre de l'article
                $post->setSlug($slugger->slug($post->getTitle())->lower());
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Posts::class,
        ]);
    }
}
