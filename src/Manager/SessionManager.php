<?php

namespace App\Manager;

use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class SessionManager
{
    /** @var EntityManagerInterface $entityManager */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function getSessionsByUser(User $user)
    {
        return $this->entityManager->getRepository(Session::class)->findByUser($user);
    }

    public function registerSession(Session $session)
    {
        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }
}
