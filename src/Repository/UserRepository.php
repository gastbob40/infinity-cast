<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use TheNetworg\OAuth2\Client\Provider\AzureResourceOwner;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function findOrCreateFromAzureOauth(AzureResourceOwner $owner): User
    {
        $user = $this->createQueryBuilder('u')
            ->where('u.azureId = :azureId')
            ->setParameters([
                'azureId' => $owner->getId()
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if ($user)
            return $user;

        $user = (new User())
            ->setAzureId($owner->getId())
            ->setEmail($owner->getUpn());

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();

        return $user;
    }


    public function findAllQuery()
    {
        return $this->createQueryBuilder('row');
    }
}
