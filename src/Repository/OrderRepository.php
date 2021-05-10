<?php

namespace App\Repository;

use App\Entity\Order;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class OrderRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 8 ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    /**
     * @return Order[] Returns an array of Order objects
     */
    
    public function findLast10()
    {
        return $this->createQueryBuilder('o')
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    public function findUserCart(User $user)
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.user = :id')
            ->setParameter('id', $user->getId())
            ->andWhere('o.status = :status')
            ->setParameter('status', Order::STATUS_CART)
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function getOrderPaginator(User $user, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('o')
            ->andWhere('o.user = :id')
            ->setParameter('id', $user->getId())
            ->andWhere('o.status = :status')
            ->setParameter('status', Order::STATUS_ORDERED)
            ->orderBy('o.updatedAt', 'DESC')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }
   
    /**
     * Finds carts that have not been modified since the given date.
     *
     * @param \DateTime $limitDate
     * @param int $limit
     *
     * @return int|mixed|string
     */
    public function findCartsNotModifiedSince(\DateTime $limitDate, int $limit = 10): array
    {
        return $this->createQueryBuilder('o')
            ->andWhere('o.status = :status')
            ->andWhere('o.updatedAt < :date')
            ->setParameter('status', Order::STATUS_CART)
            ->setParameter('date', $limitDate)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }
}
