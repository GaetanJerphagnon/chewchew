<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Restaurant|null find($id, $lockMode = null, $lockVersion = null)
 * @method Restaurant|null findOneBy(array $criteria, array $orderBy = null)
 * @method Restaurant[]    findAll()
 * @method Restaurant[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RestaurantRepository extends ServiceEntityRepository
{
    public const PAGINATOR_PER_PAGE = 6 ;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Restaurant::class);
    }

    public function getRestaurantPaginator(int $offset): Paginator
    {
        $query = $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC')
            ->andWhere('r.isActive = 1')
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->setFirstResult($offset)
            ->getQuery()
        ;

        return new Paginator($query);
    }

    public function findByCategory($value, int $offset): Paginator
    {
        $query = $this->createQueryBuilder('r')
            ->innerJoin('r.categories', 'c')
            ->andWhere('c.slug = :cat_slug')
            ->setParameter('cat_slug', $value)
            ->setMaxResults(self::PAGINATOR_PER_PAGE)
            ->orderBy('r.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->getQuery()
        ;
        
        return new Paginator($query);
    }
   
}
