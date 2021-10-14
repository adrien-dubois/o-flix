<?php

namespace App\Repository;

use App\Entity\TvShow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TvShow|null find($id, $lockMode = null, $lockVersion = null)
 * @method TvShow|null findOneBy(array $criteria, array $orderBy = null)
 * @method TvShow[]    findAll()
 * @method TvShow[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TvShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TvShow::class);
    }

    /**
     * Make a TV Show research with $title variable
     *
     * Version 1 : Query Builder
     * 
     * @param $title
     * @return tvShow[]
     */
    public function searchTvShowByTitle($title)
    {
        return $this->createQueryBuilder('tvshow')
            ->where('tvshow.title Like :title')
            ->setParameter(':title', "%$title%" )
            ->getQuery()
            ->getResult();
    }

    /**
     * Version 2 with DQL method
     *
     * @param $title
     * @return TvShow[]
     */
    public function searchTvShowByTitleDQL($title)
    {
        $entityManager = $this->getEntityManager();

        $query = $entityManager->createQuery(

            'SELECT tv
            FROM App\Entity\TvShow tv
            WHERE tv.title LIKE :title'
        )->setParameter(':title', "%$title%");

        return $query->getResult();
    }


    // /**
    //  * @return TvShow[] Returns an array of TvShow objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    
    // public function findOneBySomeField($value): ?TvShow
    // {
    //     return $this->createQueryBuilder('t')
    //         ->andWhere('t.exampleField = :val')
    //         ->setParameter('val', $value)
    //         ->getQuery()
    //         ->getOneOrNullResult()
    //     ;
    // }
    
}
