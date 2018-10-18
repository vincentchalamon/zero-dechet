<?php

/*
 * This file is part of the Zero Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;
use Gedmo\Sluggable\Util\Urlizer;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserRepository extends ServiceEntityRepository implements UserLoaderInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * @return User[]
     */
    public function findAdmins(): array
    {
        return $this->createQueryBuilder('user')
            ->where('user.roles LIKE :role')
            ->andWhere('user.active = true')
            ->setParameter('role', '%ROLE_ADMIN%')
            ->getQuery()->getResult();
    }

    public function loadUserByUsername($username): ?User
    {
        $encoding = \mb_detect_encoding($username, \mb_detect_order(), true);
        $email = Urlizer::unaccent(\mb_convert_case($username, MB_CASE_LOWER, $encoding ?: \mb_internal_encoding()));

        return $this->createQueryBuilder('u')
            ->where('u.emailCanonical = :email')
            ->andWhere('u.deletedAt IS NULL')
            ->andWhere('u.active = true')
            ->setParameter('email', $email)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
