<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagsController extends AbstractController
{
    // Route vers la page affichant la liste de tous les tags.
    #[Route('/tags', name: 'allTags')]
    public function allTags(TagsRepository $repository): Response
    {
        $tags = $repository->findAll();

        return $this->render(
            'tags/allTags.html.twig',
            [
                'tags' => $tags
            ]
        );
    }


    // Route vers la page affichant la page d'un tag selon son id.
    #[Route('/tags/{id}', name: 'tagById')]
    public function tagById(Tags $tag): Response
    {
        return $this->render(
            'tags/tag.html.twig',
            [
                'tag' => $tag
            ]
        );
    }
}
