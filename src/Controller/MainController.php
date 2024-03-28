<?php

namespace App\Controller;

use App\DataFixtures\AppFixtures;
use App\Entity\Posts;
use App\Entity\Users;
use App\Repository\PostsRepository;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{

	#[Route('/posts', name: 'allPosts')]
	/* PostsRepository contient les méthodes permettant d'aller chercher les posts depuis la base de données */
	public function allPosts(PostsRepository $repository): Response
	{
		$posts = $repository->findAll();

		return $this->render(
			'main/posts.html.twig',
			[
				'posts' => $posts
			]
		);
	}


	#[Route('/post/id', name: 'postById')]
	public function postById(PostsRepository $repository, int $id): Response
	{
		$post = $repository->find($id);

		return $this->render(
			'main/post.html.twig',
			[
				'post' => $post
			]
		);
	}
}
