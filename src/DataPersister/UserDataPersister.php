<?php

namespace App\DataPersister;

use App\Entity\User;
use ApiPlatform\Core\DataPersister\DataPersisterInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Description of UserDataPersister
 *
 * @author daniel
 */
class UserDataPersister implements DataPersisterInterface
{
	private EntityManagerInterface $entityManager;
	private UserPasswordEncoderInterface $userPasswordEncoder;

	public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $userPasswordEncoder) 
	{
		$this->entityManager = $entityManager;
		$this->userPasswordEncoder = $userPasswordEncoder;
	}
	
	public function supports($data): bool
	{
		return $data instanceof User;
	}

    /**
     * @param User $data
     */
	public function persist($data): void
    {
		if ($data->getPlainPassword()) {
			$data->setPassword(
				$this->userPasswordEncoder->encodePassword($data, $data->getPlainPassword())
			);
			$data->eraseCredentials();
        }

		$this->entityManager->persist($data);
		$this->entityManager->flush();
	}

	public function remove($data)
	{
		$this->entityManager->remove($data);
		$this->entityManager->flush();
	}
}
