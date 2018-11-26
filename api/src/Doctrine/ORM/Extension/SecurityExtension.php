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

namespace App\Doctrine\ORM\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\UserQuiz;
use App\Entity\Weighing;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class SecurityExtension implements QueryCollectionExtensionInterface
{
    private $authorizationChecker;
    private $tokenStorage;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage = $tokenStorage;
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        if (!\in_array($resourceClass, [Weighing::class, UserQuiz::class], true) || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return;
        }

        $queryBuilder
            ->andWhere(\sprintf('%s.user = :user', $queryBuilder->getRootAlias()[0]))
            ->setParameter('user', $this->tokenStorage->getToken()->getUser());
    }
}
