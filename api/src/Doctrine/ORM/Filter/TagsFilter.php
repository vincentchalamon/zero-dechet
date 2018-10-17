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

use ApiPlatform\Core\Api\IriConverterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\FilterInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Entity\Tag;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class TagsFilter implements FilterInterface
{
    private $requestStack;
    private $iriConverter;
    private $registry;

    public function __construct(RequestStack $requestStack, IriConverterInterface $iriConverter, ManagerRegistry $registry)
    {
        $this->requestStack = $requestStack;
        $this->iriConverter = $iriConverter;
        $this->registry = $registry;
    }

    public function apply(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!\property_exists($resourceClass, 'tags') || !$request || (!$request->query->has('tags') && !$request->query->has('tags_name'))) {
            return;
        }

        if ($request->query->has('tags')) {
            $iriConverter = $this->iriConverter;
            $tags = \array_map(function (string $tag) use ($iriConverter) {
                return $iriConverter->getItemFromIri($tag)->getId();
            }, $request->query->get('tags'));
        } else {
            $tags = $this->registry->getManagerForClass(Tag::class)->getRepository(Tag::class)->findBy([
                'name' => $request->query->get('tags_name'),
            ]);
        }

        foreach ($tags as $i => $tag) {
            $name = \sprintf('tag%s', $i);
            $queryBuilder
                ->join('o.tags', $name, Join::WITH, \sprintf('%1$s.id = :%1$s', $name))
                ->setParameter($name, $tag);
        }
    }

    public function getDescription(string $resourceClass): array
    {
        return [
            'tags[]' => [
                'property' => 'tags',
                'type' => 'string',
                'required' => false,
                'strategy' => SearchFilter::STRATEGY_EXACT,
            ],
        ];
    }
}
