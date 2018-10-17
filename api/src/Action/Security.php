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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class Security
{
    /**
     * @Route(name="api_login", path="/login", methods={"POST"})
     */
    public function __invoke(SerializerInterface $serializer, TokenStorageInterface $tokenStorage)
    {
        return new JsonResponse($serializer->serialize($tokenStorage->getToken()->getUser(), ItemNormalizer::FORMAT), JsonResponse::HTTP_OK, [
            'Content-Type' => 'application/ld+json',
        ], true);
    }
}
