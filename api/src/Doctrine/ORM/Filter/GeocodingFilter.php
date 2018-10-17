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
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class GeocodingFilter implements FilterInterface
{
    private const DEFAULT_DISTANCE = 10000;

    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || !\property_exists($resourceClass, 'coordinates')
            || !$request->query->has('latitude') || empty($request->query->get('latitude'))
            || !$request->query->has('longitude') || empty($request->query->get('longitude'))
        ) {
            return;
        }

        $queryBuilder->andWhere(\sprintf(<<<'SQL'
ST_Distance_Sphere(ST_GeomFromText('POINT(%s %s)'), o.coordinates) < :distance
SQL
            , $request->query->get('longitude'), $request->query->get('latitude')
        ))->setParameter('distance', $request->query->getInt('distance', self::DEFAULT_DISTANCE));
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'latitude' => [
                'property' => 'latitude',
                'type' => 'string',
                'required' => false,
                'strategy' => SearchFilter::STRATEGY_EXACT,
            ],
            'longitude' => [
                'property' => 'longitude',
                'type' => 'string',
                'required' => false,
                'strategy' => SearchFilter::STRATEGY_EXACT,
            ],
        ];
    }
}
