<?php

namespace App\Controller\Admin;

use App\Entity\Posts;
use App\Form\PostType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

#[Route('/admin/blog', name: 'admin_blog_')]
final class BlogController extends AbstractController
{
  // Route vers la page affichant l'ensemble des posts
  // On précise le type de requête utilisée, ici ['GET']
  #[Route('/posts', name: 'all_posts', methods: ['GET'])]
  public function allPosts(PostsRepository $repository): Response
  {
    $posts = $repository->findAll();

    return $this->render(
      'admin/blog/allPosts.html.twig',
      [
        'posts' => $posts
      ]
    );
  }


  // Route vers la page contenant le formulaire de création de post
  // On précise le type de requête utilisée, ici ['GET','POST']
  #[Route('/post/create', name: 'create_post', methods: ['GET', 'POST'])]
  public function createPost(Request $request, EntityManagerInterface $em, UsersRepository $repository): Response
  {
    $user = $repository->findOneBy(['username' => 'jane_admin']);
    // Instanciation de chaque nouveau post créé.
    $post = new Posts();
    $post->setAuthor($user);

    // Création du formulaire selon les champs précisé dans le fichier PostType.
    $form = $this->createForm(PostType::class, $post);
    // Permet de traiter la requête et récupérer les données soumises.
    $form->handleRequest($request);

    // On vérifie si la réquête est bien soumise et que les informations sont valides.
    if ($form->isSubmitted() && $form->isValid()) {
      $em->persist($post);
      $em->flush();

      // Redirection vers la page affichant tous les posts.
      return $this->redirectToRoute('all_posts', status: Response::HTTP_SEE_OTHER);
    }
    // Si la requête est incorrecte, redirection vers la page de formulaire de création de post.
    return $this->render(
      'admin/blog/createPost.html.twig',
      [
        'form' => $form,
        'post' => $post
      ]
    );
  }


  // Route vers la page contenant le formulaire de modification de post
  // On précise le type de requête utilisée, ici ['GET','POST']
  #[Route('/post/edit/{id}', name: 'edit_post', methods: ['GET', 'POST'])]
  public function editPost(Posts $post, Request $request, EntityManagerInterface $em): Response
  {

    // Création du formulaire selon les champs précisé dans le fichier PostType.
    $form = $this->createForm(PostType::class, $post);
    // Permet de traiter la requête et récupérer les données soumises.
    $form->handleRequest($request);

    // On vérifie si la réquête est bien soumise et que les informations sont valides.
    if ($form->isSubmitted() && $form->isValid()) {
      $em->flush();

      // Redirection vers la page affichant tous les posts.
      return $this->redirectToRoute(
        'admin_blog_post_by_id',
        [
          'id' => $post->getId()
        ],
        status: Response::HTTP_SEE_OTHER
      );
    }
    // Si la requête est incorrecte, redirection vers la page de formulaire de création de post.
    return $this->render(
      'admin/blog/modifPost.html.twig',
      [
        'form' => $form,
        'post' => $post
      ]
    );
  }


  // Route vers la page affichant un post selon son id
  // On précise le type de requête utilisée, ici ['GET']
  #[Route('/post/{id}', name: 'post_by_id', methods: ['GET'])]
  /**
   * Permet d'afficher un post selon son id
   */
  public function postById(Posts $post): Response
  {
    return $this->render(
      'admin/blog/post.html.twig',
      [
        'post' => $post
      ]
    );
  }
}
