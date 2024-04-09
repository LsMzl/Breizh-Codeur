<?php

namespace App\Controller;

use App\Entity\Posts;
use App\Entity\Users;
use App\Entity\Comments;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use App\Repository\CommentsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MainController extends AbstractController
{

	#[Route('/', name: 'index')]
	/* PostsRepository contient les méthodes permettant d'aller chercher les posts depuis la base de données */
	public function index(PostsRepository $postRepo, UsersRepository $userRepo): Response
	{
		// Récupération des 5 derniers articles publiés
		$posts = $postRepo->findBy([], ['published_at'=> 'DESC'], 5);
		// Récupération des 5 derniers utilisateurs inscrits
		$users = $userRepo->findBy([], ['id'=> 'DESC'], 5);

		return $this->render(
			'main/index.html.twig',
			[
				'posts' => $posts,
				'users' => $users
			]
		);
	}

	#[Route('/posts', name: 'allPosts')]
	/* PostsRepository contient les méthodes permettant d'aller chercher les posts depuis la base de données */
	public function allPosts(PostsRepository $repository): Response
	{
		$posts = $repository->findAll();


		return $this->render(
			'main/posts.html.twig', compact('posts')			
		);
	}


	#[Route('/post/{id}', name: 'show', requirements:['id' => Requirement::DIGITS] )]
	/**
	 * Permet d'afficher un post selon son id
	 */
	public function show(Posts $post): Response
	{

		return $this->render(
			'main/post.html.twig', compact('post')
		);
	}



	#[Route('/user/{id}', name: 'user_profile', requirements:['id' => Requirement::DIGITS] )]
	/**
	 * Permet d'afficher un post selon son id
	 */
	public function userProfile(#[CurrentUser] Users $user, CommentsRepository $commentRepo, PostsRepository $postRepo): Response
	{
		$comments = $commentRepo->findBy(
			[ 'author' => $user ]
		);
		$posts = $postRepo->findBy(
			[ 'author' => $user ]
		);

		return $this->render(
			'main/userProfile.html.twig',
			 [
				'user'=> $user,
				'comments'=> $comments,
				'posts'=> $posts
			 ]
		);
	}
}
