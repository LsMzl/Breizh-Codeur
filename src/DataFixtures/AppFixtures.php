<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use App\Entity\Users;
use App\Entity\Comments;
use App\Entity\Posts;
use DateTimeImmutable;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\String\u;

class AppFixtures extends Fixture
{
	/**
	 * Constructeur
	 * @param UserPasswordHasherInterface $hasher l'instance du service hashage de mots de passe
	 * 
	 */
	public function __construct(
		// On charge l'outil de hashage de mot de passe
		private readonly UserPasswordHasherInterface $hasher,
		// On charge l'outil qui permet de créer un slug
		private readonly SluggerInterface $slugger
	) {
	}

	/*********************************************************************/
	//														LOADERS																 //
	//	   Permettent d'envoyer les données sur la base de données       //
	/*********************************************************************/

	/**
	 * Charge les données de base dans la base de données
	 * @param ObjectManager $manager Gestionnaire d'entities pour l'opération de persistance
	 */
	public function load(ObjectManager $manager): void
	{
		//On passe $manager en paramètre afin d'avoir accès aux fonctions persist et flush
		$this->loadUsers($manager);
		$this->loadTags($manager);
		$this->loadPosts($manager);
		$this->loadComments($manager);
	}

	/**
	 * Charge les données des utilisateurs dans la base de données
	 * @param ObjectManager $manager Gestionnaire d'entities pour l'opération de persistance
	 * @return void
	 */
	private function loadUsers(ObjectManager $manager): void
	{
		//Pour chaque Users on définit les infos des users
		foreach ($this->getUsersDatas() as [$fullName, $userName, $password, $email, $role]) {
			$user = new Users();																						//Création d'une nouvelle instance de l'entité Users
			$user->setFullName($fullName)																		//Nom de l'utilisateur
				->setUsername($userName)																			//Pseudo de l'utilisateur			
				->setPassword($this->hasher->hashPassword($user, $password))	//Hashage du mot de passe
				->setEmail($email)																						//Mail de l'utilisateur
				->setRoles($role);																						//Rôle de l'utilisateur

			// On stocke les données de $user
			$manager->persist($user);

			//Ajout d'une référence à l'utilisateur pour une utilisation dans les tests
			$this->addReference($userName, $user);
		}
		// On envoie les données de $user sur la Bdd
		$manager->flush();
	}

	/**
	 * Charge les données des tags dans la base de données
	 * @param ObjectManager $manager Gestionnaire d'entities pour l'opération de persistance
	 * @return void
	 */
	private function loadTags(ObjectManager $manager): void
	{
		//Pour chaque Tag on définit les infos des tags
		foreach ($this->getTagsData() as $name) {
			$tag = new Tags($name);																					//Création d'une nouvelle instance de l'entité Tags

			// On stocke les données de $tag
			$manager->persist($tag);

			//Ajout d'une référence au tag pour une utilisation dans les tests
			$this->addReference("tag-$name", $tag);
		}
		// On envoie les données de $tag sur la Bdd
		$manager->flush();
	}

	/**
	 * 
	 */
	private function loadComments(ObjectManager $manager): void
	{

		foreach ($this->getCommentsDatas() as [$post, $content, $published_at, $author]) {
			$comment = new Comments();
			$comment->setPost($post)
				->setContent($content)
				->setPublishedAt($published_at)
				->setAuthor($author);

			// On stocke les données de $user
			$manager->persist($comment);

			//Ajout d'une référence
			// $this->addReference();
		}
		// On envoie les données de $user sur la Bdd
		$manager->flush();
	}

	/**
	 * Charge des publications fictives dans la base de données.
	 * 
	 * @param ObjectManager $manager Le gestionnaire d'entitites pour l'opération de persistance.
	 * @return void
	 */
	private function loadPosts(ObjectManager $manager): void
	{
		//Parcourt les données de publication obtenues depuis une source quelconque.
		foreach ($this->getPostsDatas() as [$title, $slug, $summary, $content, $publishedAt, $author, $tags]) {
			//Création d'une nouvelle instance de l'entité Posts
			$post = new Posts();
			//Définition des propriétés de la publication.
			$post->setTitle($title)
				->setSlug($slug)
				->setSummary($summary)
				->setContent($content)
				->setPublishedAt($publishedAt)
				->setAuthor($author)
				//Ajout des tags à la piblication
				->addTag(...$tags);
			//...$tags prend chaque élément du tableau $tags et le traite comme un argument distinct.

			//Ajout de commentaires fictifs à la publication.
			foreach (range(1, 5) as $i) {
				//Récupération de l'auteur du commentaire fictif.
				/** @var Users $commentAuthor*/
				$commentAuthor = $this->getReference('john_user');

				//Crzéation d'une nouvelle instance de l'entité Comments
				$comment = new Comments();
				//Définition des propriétés du commentaire.
				$comment->setAuthor($commentAuthor)
					->setContent($this->getRandomText(random_int(255, 512)))
					->setPublishedAt(new \DateTimeImmutable('now - ' . $i . 'minutes'))
					->setPost($post);

				// On stocke les données de $comment
				$manager->persist($comment);
			}
			// On stocke les données de $post
			$manager->persist($post);
		}
		// On envoie les données de $comment et $post sur la Bdd
		$manager->flush();
	}

	/*********************************************************************/
	//														GETTER																 //
	//	        Permettent de définir les données des entités            //
	/*********************************************************************/

	/**
	 * Permet de récupérer les données des utilisateurs
	 * @return array<array{string, string, string, string, array<string>}>
	 */
	private function getUsersDatas(): array
	{
		return [
			// $userData = [$fullName, $userName, $password, $email, $role]
			['Jane Doe', 'jane_admin', 'kitten', 'jane_admin@symfony.com', ['ROLE_ADMIN']],
			['Tom Doe', 'tom_admin', 'kitten', 'tom_admin@symfony.com', ['ROLE_ADMIN']],
			['John Doe', 'john_admin', 'kitten', 'john_user@symfony.com', ['ROLE_USER']]
		];
	}

	/**
	 * Génére des données de publication pour les tests.
	 * @throws \Exception Si une erreur survient lors de la génération des données aléatoires.
	 * @return array<int, array{0: string, 1: string, 2: string, 3: string, 4: \DateTimeImmutable, 5: Users, 6: array<Tags>}> Un tableau contenant des données de publication.
	 */
	private function getPostsDatas(): array
	{
		//Initialisation d'un tableau pour stocker les données de publication.
		$posts = [];

		//Parcourt les phrases obtenues depuis une source quelconque.
		foreach ($this->getPhrases() as $i => $title) {
			//Génération des données pour chaque publication.

			//Récupération aléatoire d'un utilisateur de référence (Jane Doe ou Tom Doe => Admin) pour l'auteur de la publication.
			/** @var Users $user */
			$user = $this->getReference(['jane_admin', 'tom_admin'][0 === $i ? 0 : random_int(0, 1)]);

			//Construction d'un tableau représentant les données de publication.
			$posts[] = [
				$title,																				//0: Titre de la publication
				$this->slugger->slug($title)->lower(),				//1: Slug généré à partir du titre
				$this->getRandomText(),												//2: Résumé aléatoire de la publication
				$this->getPostContent(),											//3: Contenu aléatoire de la publication
				(new \DateTime('now - ' . $i . 'days'))				//4: Date de publication calculée en fonction de l'index et de l'heure aléatoire
					->setTime(
						random_int(8, 17),
						random_int(7, 49),
						random_int(0, 59)
					),
				$user,																				//5: Auteur de la publication
				$this->getRandomTags(),												//6: Tags aléatoires associés à la publication
			];
		}
		//Retourne le tableau de données de publication généré
		return $posts;
	}

	/**
	 * Permet de récupérer les données des tags
	 * @return string[] Tableau contenant les différents noms des tags
	 */
	private function getTagsData(): array
	{
		return [
			'lorem',
			'ipsum',
			'consectetur',
			'adipiscing',
			'incididunt',
			'labore',
			'voluptate',
			'dolore',
			'pariatur',
		];
	}

	private function getCommentsDatas(): array
	{
		return [
			// $commentData = [$post, $content, $published_at, $author]
			['post', 'contenu du commentaire', 'date de publication', 'auteur'],
			['post', 'ceci est un commentaire', 'publié le', 'auteur']
		];
	}

	/**
	 * @return string[]
	 */
	private function getPhrases(): array
	{
		return [
			'Lorem ipsum dolor sit amet consectetur adipiscing elit',
			'Pellentesque vitae velit ex',
			'Mauris dapibus risus quis suscipit vulputate',
			'Eros diam egestas libero eu vulputate risus',
			'In hac habitasse platea dictumst',
			'Morbi tempus commodo mattis',
			'Ut suscipit posuere justo at vulputate',
			'Ut eleifend mauris et risus ultrices egestas',
			'Aliquam sodales odio id eleifend tristique',
			'Urna nisl sollicitudin id varius orci quam id turpis',
			'Nulla porta lobortis ligula vel egestas',
			'Curabitur aliquam euismod dolor non ornare',
			'Sed varius a risus eget aliquam',
			'Nunc viverra elit ac laoreet suscipit',
			'Pellentesque et sapien pulvinar consectetur',
			'Ubi est barbatus nix',
			'Abnobas sunt hilotaes de placidus vita',
			'Ubi est audax amicitia',
			'Eposs sunt solems de superbus fortis',
			'Vae humani generis',
			'Diatrias tolerare tanquam noster caesium',
			'Teres talis saepe tractare de camerarius flavum sensorem',
			'Silva de secundus galatae demitto quadra',
			'Sunt accentores vitare salvus flavum parses',
			'Potus sensim ad ferox abnoba',
			'Sunt seculaes transferre talis camerarius fluctuies',
			'Era brevis ratione est',
			'Sunt torquises imitari velox mirabilis medicinaes',
			'Mineralis persuadere omnes finises desiderium',
			'Bassus fatalis classiss virtualiter transferre de flavum',
		];
	}

	private function getPostContent(): string
	{
		return <<<'MARKDOWN'
            Lorem ipsum dolor sit amet consectetur adipisicing elit, sed do eiusmod tempor
            incididunt ut labore et **dolore magna aliqua**: Duis aute irure dolor in
            reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.
            Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia
            deserunt mollit anim id est laborum.

              * Ut enim ad minim veniam
              * Quis nostrud exercitation *ullamco laboris*
              * Nisi ut aliquip ex ea commodo consequat

            Praesent id fermentum lorem. Ut est lorem, fringilla at accumsan nec, euismod at
            nunc. Aenean mattis sollicitudin mattis. Nullam pulvinar vestibulum bibendum.
            Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos
            himenaeos. Fusce nulla purus, gravida ac interdum ut, blandit eget ex. Duis a
            luctus dolor.

            Integer auctor massa maximus nulla scelerisque accumsan. *Aliquam ac malesuada*
            ex. Pellentesque tortor magna, vulputate eu vulputate ut, venenatis ac lectus.
            Praesent ut lacinia sem. Mauris a lectus eget felis mollis feugiat. Quisque
            efficitur, mi ut semper pulvinar, urna urna blandit massa, eget tincidunt augue
            nulla vitae est.

            Ut posuere aliquet tincidunt. Aliquam erat volutpat. **Class aptent taciti**
            sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Morbi
            arcu orci, gravida eget aliquam eu, suscipit et ante. Morbi vulputate metus vel
            ipsum finibus, ut dapibus massa feugiat. Vestibulum vel lobortis libero. Sed
            tincidunt tellus et viverra scelerisque. Pellentesque tincidunt cursus felis.
            Sed in egestas erat.

            Aliquam pulvinar interdum massa, vel ullamcorper ante consectetur eu. Vestibulum
            lacinia ac enim vel placerat. Integer pulvinar magna nec dui malesuada, nec
            congue nisl dictum. Donec mollis nisl tortor, at congue erat consequat a. Nam
            tempus elit porta, blandit elit vel, viverra lorem. Sed sit amet tellus
            tincidunt, faucibus nisl in, aliquet libero.
            MARKDOWN;
	}

	/**
	 * Génére du texte aléatoire en concatenant des phrases aléaroires 
	 * jusqu'à atteindre une longueur max spécifiée
	 * 
	 * @param int $maxLength Longueur maximale du texte généré. Par défaut, 255 chars.
	 * @return string Texte généré aléatoirement.
	 */
	private function getRandomText(int $maxLength = 255): string
	{
		//Récupération des phrases
		$phrases = $this->getPhrases();

		//Mélange aléatoire des phrases
		shuffle($phrases);

		//Concaténation des phrases mélangées jusqu'à un nombre de caractères max donné
		do {
			//Concaténation des phrases avec un point d'arrêt à la fin de chaque phrases.
			//Bibliothèque "PHP Microtime uString".
			$text = u('. ')->join($phrases)->append('.');

			//Suppresion de la dernière phrase du tableau pour raccourcir le texte.
			array_pop($phrases);
			//Vérification de la longueur du texte généré
		} while ($text->length() > $maxLength);

		//On retourne le texte généré
		return $text;
	}

	/**
	 * Génére un tableau d'objets tags aléatoires.
	 * 
	 * @throws \Exception Si une erreur survient lors de la génération des nombres aléatoires.
	 * @return array<Tags> Tableau contenant des objets Tags aléatoires.
	 */
	private function getRandomTags(): array
	{
		//Récupération des tags.
		$tagNames = $this->getTagsData();

		//Mélange aléatoire des noms de tag.
		shuffle($tagNames);

		//Sélection aléatoire de 2 à 4 noms de tag.
		$selectedTags = \array_slice($tagNames, 0, random_int(2, 4));

		//Création d'un tableau d'objets tag à partir des noms de tag sélectionnés
		return array_map(function ($tagName) {

			//Récupération de l'objet tag correspondant au nom de tag à partir de références.
			/** @var Tags $tag */
			$tag = $this->getReference("tag-$tagName");

			return $tag;
		}, $selectedTags);
	}
}
