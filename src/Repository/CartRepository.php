<?php

namespace App\Repository;

use App\Entity\Cart;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Cart|null find($id, $lockMode = null, $lockVersion = null)
 * @method Cart|null findOneBy(array $criteria, array $orderBy = null)
 * @method Cart[]    findAll()
 * @method Cart[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CartRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Cart::class);
    }

    /**
     * @param UserInterface $user
     * @return Cart
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function getCurrentCart(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('c')
            ->select('partial c.{id, total, status, state, fees}')
            ->addSelect('partial user.{id, username, email}')
            ->addSelect('partial userMetadata.{id, firstname, lastname}')
            ->addSelect('partial delivery.{id}')
            ->addSelect('partial cartItem.{id, name, code, quantity, price, createdAt, updatedAt}')
            ->addSelect('partial product.{id, name, slug, keywords, translations, brand, alias, price, createdAt, updatedAt}')
            ->addSelect('partial session.{id, customTitle, comment, groupBooking, start, end, details, pause, price, age, level, nbPersons, duration}')
            ->addSelect('partial sport.{id, name, translations}')
            ->addSelect('partial coach.{id, username, email}')
            ->addSelect('partial coachUserMetadata.{id, firstname, lastname, slug}')
            ->leftJoin('c.user', 'user')
            ->leftJoin('c.delivery', 'delivery')
            ->leftJoin('user.userMetadata', 'userMetadata')
            ->leftJoin('c.items', 'cartItem')
            ->leftJoin('cartItem.product', 'product')
            ->leftJoin('cartItem.session', 'session')
            ->leftJoin('session.sport', 'sport')
            ->leftJoin('session.user', 'coach')
            ->leftJoin('coach.userMetadata', 'coachUserMetadata')
            ->andWhere('user.id = :user')
            ->andWhere('c.status = :status')
            ->andWhere('c.state != :state')
            ->setParameter('status', 'current')
            ->setParameter('state', 'confirmation')
            ->setParameter('user', $user)
            ->setMaxResults(1);

        $cart = null;
        try {
            $cart = $qb->getQuery()
                ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true)
                ->getOneOrNullResult();
        } catch (NonUniqueResultException $exception) {
        }
        if (!$cart) {
            $cart = new Cart();
            $cart->setUser($user)
                ->setTotal(0)
                ->setDelivery(null)
                ->setStatus('current')
                ->setState('cart')
                ->setFees(0);

            $this->getEntityManager()->persist($cart);
            $this->getEntityManager()->flush();
        }

        return $cart;
    }
}
