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
use App\Authorization\AuthorizationCheckerInterface;
use App\Entity\User;
use App\Score\ScoreManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserScore
{
    /**
     * @Route(name="api_users_scores_item", path="/users/{id}/scores", methods={"GET"})
     */
    public function __invoke(User $data, ScoreManager $manager, SerializerInterface $serializer, TokenStorageInterface $tokenStorage, AuthorizationCheckerInterface $authorizationChecker): JsonResponse
    {
        // Cannot use @Security cause it uses another AuthorizationChecker service than ApiPlatform (overriden in App\Authorization\AuthorizationChecker)
        if (!$authorizationChecker->isFeatureEnabled('quiz') || !($authorizationChecker->isGranted('ROLE_ADMIN') || ($authorizationChecker->isGranted('ROLE_ADMIN_CITY') && $authorizationChecker->isInTheSameCity($data->getProfile())) || $data === $tokenStorage->getToken()->getUser())) {
            throw new AccessDeniedHttpException();
        }

        return new JsonResponse($serializer->serialize($manager->getScores($data), ItemNormalizer::FORMAT, ['api_sub_level' => true]), 200, [], true);
    }
}
