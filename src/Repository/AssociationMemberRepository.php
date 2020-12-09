<?php

namespace App\Repository;

use App\Entity\AssociationMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method AssociationMember|null find($id, $lockMode = null, $lockVersion = null)
 * @method AssociationMember|null findOneBy(array $criteria, array $orderBy = null)
 * @method AssociationMember[]    findAll()
 * @method AssociationMember[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AssociationMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AssociationMember::class);
    }

    // /**
    //  * @return AssociationMember[] Returns an array of AssociationMember objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AssociationMember
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
