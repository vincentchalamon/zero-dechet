<?php

/*
 * This file is part of the Zero-Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Doctrine\ORM\Filter;

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use ApiPlatform\Core\Exception\ItemNotFoundException;
use App\Entity\Weighing;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class WeighingsTeamFilter implements FilterInterface
{
    private $requestStack;
    private $iriConverter;

    public function __construct(RequestStack $requestStack, IriConverterInterface $iriConverter)
    {
        $this->requestStack = $requestStack;
        $this->iriConverter = $iriConverter;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!\is_a($resourceClass, Weighing::class, true) || !$request->query->has('team')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        try {
            $users = $this->iriConverter->getItemFromIri($request->query->get('team'))->getUsers();
        } catch (ItemNotFoundException $exception) {
            $users = [];
        }
        $queryBuilder->andWhere($queryBuilder->expr()->in($rootAlias.'.user', ':users'))->setParameter('users', $users);
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'team' => [
                'property' => 'team',
                'type' => 'string',
                'required' => false,
                'strategy' => SearchFilter::STRATEGY_EXACT,
            ],
        ];
    }
}
