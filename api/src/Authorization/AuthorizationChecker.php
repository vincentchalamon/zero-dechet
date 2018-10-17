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

namespace App\Authorization;

use App\Entity\Profile;
use App\Entity\Shop;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as BaseAuthorizationCheckerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class AuthorizationChecker implements AuthorizationCheckerInterface
{
    private $decorated;
    private $tokenStorage;
    private $features;

    public function __construct(BaseAuthorizationCheckerInterface $decorated, TokenStorageInterface $tokenStorage, array $features = [])
    {
        $this->decorated = $decorated;
        $this->tokenStorage = $tokenStorage;
        $this->features = $features;
    }

    public function isFeatureEnabled(string $name): bool
    {
        return \in_array($name, $this->features, true);
    }

    public function isGranted($attributes, $subject = null): bool
    {
        return $this->decorated->isGranted($attributes, $subject);
    }

    /**
     * @param Profile|Shop $object
     */
    public function isInTheSameCity($object, $user = null): bool
    {
        if (null === $user) {
            $user = $this->tokenStorage->getToken()->getUser();
        }

        return \in_array($object->getCity(), $user->getCities(), true);
    }
}
