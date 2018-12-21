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

namespace App\Action;

use ApiPlatform\Core\JsonLd\Serializer\ItemNormalizer;
use App\Entity\User;
use App\Score\ScoreManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserScores
{
    public function __invoke(User $data, ScoreManager $manager, SerializerInterface $serializer): JsonResponse
    {
        return new JsonResponse($serializer->serialize($manager->getScores($data), ItemNormalizer::FORMAT, [
            'api_sub_level' => true,
            'groups' => ['score:read'],
        ]), 200, [], true);
    }
}
