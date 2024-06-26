<?php

namespace App\Controller\Admin;

use App\Entity\Posts;
use App\Form\PostType;
use App\Repository\PostsRepository;
use App\Repository\UsersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/admin/blog', name: 'admin_blog_')]
final class BlogController extends AbstractController
{
  // Route vers la page affichant l'ensemble des posts
  // On précise le type de requête utilisée, ici ['GET']
  #[Route('/index', name: 'index', methods: ['GET'])]
  public function index(): Response
  {
    return $this->render(
      'admin/index.html.twig',
      [
      ]
    );
  }


  // Route vers la page affichant l'ensemble des posts
  // On précise le type de requête utilisée, ici ['GET']
  #[Route('/posts', name: 'all_posts', methods: ['GET'])]
  public function allPosts(PostsRepository $repository): Response
  {
    // Récupération des posts par date de publication décroissante
    $posts = $repository->findBy([], orderBy: ['published_at' => 'DESC']);

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

    //DEFINITION D'UN UTILISATEUR PAR DEFAUT AVEC INJECTION DE DEPENDANCE
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
      return $this->redirectToRoute('admin_blog_all_posts', status: Response::HTTP_SEE_OTHER);
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

  // Route vers la page affichant un post selon son id
  // On précise le type de requête utilisée, ici ['POST]
  #[Route('/post/{id}', name: 'delete_post', methods: ['DELETE'])]
  public function deletePost(Posts $post, EntityManagerInterface $em, Request $request): Response
  {
    // Récupération du token csrf
    /** @var string|null $rtoken */
    $token = $request->getPayload()->get('token');

    if($this->isCsrfTokenValid('delete', $token)) 
    {
      // Suppression du post
      $em->remove($post);
      // Mise à jour des données
      $em->flush();
    }

    // Redirection
    return $this->redirectToRoute(
      'admin_blog_all_posts',
      status: Response::HTTP_SEE_OTHER
    );
  }


  // Route vers la page affichant l'ensemble des utilisateurs
  // On précise le type de requête utilisée, ici ['GET']
  #[Route('/users', name: 'all_users', methods: ['GET'])]
  public function allUsers(UsersRepository $repository): Response
  {
    // Récupération des posts par date de publication décroissante
    $users = $repository->findBy([], orderBy: ['full_name' => 'ASC']);

    return $this->render(
      'admin/allUsers.html.twig',
      [
        'users' => $users
      ]
    );
  }
}
