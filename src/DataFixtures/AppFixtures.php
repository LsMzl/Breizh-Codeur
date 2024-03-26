<?php

namespace App\DataFixtures;

use App\Entity\Tags;
use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
	public function __construct(
		// On charge l'outil de hashage de mot de passe
		private readonly UserPasswordHasherInterface $hasher
	) {
	}

	public function load(ObjectManager $manager): void
	{
		//On utilise la fonction loadUsers en lui passant $manager en paramètre afin d'avoir accès aux fonction persist et flush
		$this->loadUsers($manager);

		$manager->flush();
	}

	/**
	 * 
	 */
	private function loadUsers(ObjectManager $manager): void
	{
		//Pour chaque Users on définit les infos des users
		foreach ($this->getUsersDatas() as [$fullName, $userName, $password, $email, $role]) {
			$user = new Users();
			$user->setFullName($fullName)
				->setUsername($userName)
				//On hash le mot de passe
				->setPassword($this->hasher->hashPassword($user, $password))
				->setEmail($email)
				->setRoles($role);

			// On stocke les données de $user
			$manager->persist($user);

			//Ajout d'une référence
			$this->addReference($userName, $user);
		}
		// On envoie les données de $user sur la Bdd
		$manager->flush();
	}

	/**
	 * 
	 */
	private function loadTags(ObjectManager $manager): void
	{
		foreach ($this->getTagsData() as $name) {
			$tag = new Tags($name);
			// $tag->setName($name);

			// On stocke les données de $tag
			$manager->persist($tag);

			//Ajout d'une référence
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
	}

	/**
	 * 
	 */
	private function loadPosts(ObjectManager $manager): void
	{
	}

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
	 * Permet de récupérer les données des tags
	 * @return string[]
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
}
