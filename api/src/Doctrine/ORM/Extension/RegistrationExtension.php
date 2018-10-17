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
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryBuilderHelper;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Registration;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class RegistrationExtension implements QueryCollectionExtensionInterface
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
        if (!\is_a($resourceClass, Registration::class, true) || $this->authorizationChecker->isGranted('ROLE_ADMIN')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $eventAlias = QueryBuilderHelper::addJoinOnce($queryBuilder, $queryNameGenerator, $rootAlias, 'event');
        $queryBuilder->andWhere(
            $queryBuilder->expr()->orX(
                $queryBuilder->expr()->eq(\sprintf('%s.organizer', $eventAlias), ':user'),
                $queryBuilder->expr()->eq(\sprintf('%s.status', $rootAlias), ':status')
            )
        )->setParameter('status', Registration::STATUS_VALIDATED)
         ->setParameter('user', $this->tokenStorage->getToken()->getUser());
    }
}
