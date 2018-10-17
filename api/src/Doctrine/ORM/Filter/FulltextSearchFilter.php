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

namespace App\Doctrine\ORM\Filter;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Content;
use App\Exception\MissingArgumentException;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class FulltextSearchFilter implements FilterInterface
{
    private $requestStack;
    private $properties;

    public function __construct(RequestStack $requestStack, array $properties = null)
    {
        if (empty($properties)) {
            throw new MissingArgumentException('The option "properties" is required in FulltextSearchFilter.');
        }

        $this->requestStack = $requestStack;
        $this->properties = \array_keys($properties);
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!\is_a($resourceClass, Content::class, true) || !$request->query->has('search')) {
            return;
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];
        $dql = \implode(' OR ', \array_map(function (string $property) use ($rootAlias) {
            return \sprintf('%s.%s LIKE :search', $rootAlias, $property);
        }, $this->properties));
        $queryBuilder->andWhere($dql)->setParameter('search', '%'.$request->query->get('search').'%');
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'search' => [
                'property' => 'search',
                'type' => 'string',
                'required' => false,
                'strategy' => SearchFilter::STRATEGY_PARTIAL,
            ],
        ];
    }
}
