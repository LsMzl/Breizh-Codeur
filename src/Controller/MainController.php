<?php

namespace App\Controller;

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
	#[Route('/', name: 'index')]
	/* EntityManagerInterface est un objet permettant de gérer les entités */
	// public function index(EntityManagerInterface $em): Response
	// {

	// 	/* Création d'une variable $author contenant l'objet Users */
	// 	$author = new Users();
	// 	$author->setFullName("Louis Mazzella")
	// 		->setEmail("aze@aze.fr")
	// 		->setUsername("IronHat")
	// 		->setPassword("motDePasse");

	// 	$em->persist($author);

	// 	/* Création d'une variable $post contenant l'objet Posts */
	// 	$post = new Posts();
	// 	$post->setTitle("Un titre")
	// 		->setContent("Ceci est le contenu du post")
	// 		->setSummary("Ceci est un résumé")
	// 		->setPublishedAt(new \DateTimeImmutable())
	// 		->setSlug("Un-titre")
	// 		->setAuthor($author);

	// 	/* persist permet de sauvegarder temporairement les données
	//       flush permet d'envoyer dans la BdD les données persistées */
	// 	$em->persist($post);

	// 	$em->flush();

	// 	return $this->render('main/index.html.twig', [
	// 		'controller_name' => 'MainController',
	// 	]);
	// }

	/* PostsRepository contient les méthodes permettant d'aller chercher les posts depuis la base de données */
	/*public function index(PostsRepository $repository): Response
	{
		// Création de $posts permettant de récupérer tous les posts
		$posts = $repository->findAll();

		dd($posts);
	}*/

	/* PostsRepository contient les méthodes permettant d'aller chercher les posts depuis la base de données */
	public function index(PostsRepository $repository): Response
	{
		$posts = $repository->findByTitle("Un titre");
		dd($posts);
	}
}
