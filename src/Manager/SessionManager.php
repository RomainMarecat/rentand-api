<?php

namespace App\Manager;

use App\Entity\OnlineSession;
use App\Entity\Session;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
        /** @var OnlineSession $onlineSession */
        $onlineSession = $this->entityManager->getRepository(OnlineSession::class)
            ->find($session->getOnlineSession()->getId());

        $session->setPrice($onlineSession->getPrice());

        $this->entityManager->persist($session);
        $this->entityManager->flush();

        return $session;
    }

    public function removeSession(UserInterface $user, Session $session)
    {
        $session = $this->entityManager->getRepository(Session::class)
            ->findSessionByCustomer($session, $user);

        $this->entityManager->remove($session);
        $this->entityManager->flush();
    }
}
