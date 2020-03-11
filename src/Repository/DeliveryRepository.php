<?php

namespace App\Repository;

use App\Entity\Cart;
use App\Entity\Delivery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @method Delivery|null find($id, $lockMode = null, $lockVersion = null)
 * @method Delivery|null findOneBy(array $criteria, array $orderBy = null)
 * @method Delivery[]    findAll()
 * @method Delivery[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DeliveryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Delivery::class);
    }

    public function getDeliveries(UserInterface $user)
    {
        $qb = $this->createQueryBuilder('d')
            ->addSelect('partial billing.{id, street, streetComplement, email, firstname, lastname, zipcode, country, city}')
            ->addSelect('partial address.{id, street, streetComplement, email, firstname, lastname, zipcode, country, city}')
            ->leftJoin('d.user', 'user')
            ->leftJoin('d.billing', 'billing')
            ->leftJoin('d.address', 'address')
            ->andWhere('user.id = :user')
            ->setParameter('user', $user);

        $query = $qb->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);

        return $query->getResult();
    }

    public function getDelivery(UserInterface $user, Cart $cart)
    {
        $qb = $this->createQueryBuilder('d')
            ->addSelect('partial billing.{id, street, streetComplement, email, firstname, lastname, zipcode, country, city}')
            ->addSelect('partial address.{id, street, streetComplement, email, firstname, lastname, zipcode, country, city}')
            ->leftJoin('d.user', 'user')
            ->leftJoin('d.billing', 'billing')
            ->leftJoin('d.address', 'address')
            ->leftJoin('d.carts', 'cart')
            ->andWhere('user.id = :user')
            ->andWhere('cart.id = :cart')
            ->setMaxResults(1)
            ->setParameter('user', $user)
            ->setParameter('cart', $cart);

        $query = $qb->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, true);

        return $query->getOneOrNullResult();
    }

    // /**
    //  * @return Delivery[] Returns an array of Delivery objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Delivery
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
