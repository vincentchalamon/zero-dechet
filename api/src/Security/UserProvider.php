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

namespace App\Security;

use App\Entity\User;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class UserProvider implements UserProviderInterface
{
    private $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function loadUserByUsername($username): User
    {
        /** @var User|null $user */
        if (null === ($user = $this->registry->getRepository(User::class)->findOneBy([
            'email' => $username,
            'active' => true,
            'deletedAt' => null,
        ]))) {
            throw new UsernameNotFoundException(\sprintf('The user "%s" does not exist.', $username));
        }

        return $user;
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        $this->registry->getManagerForClass(User::class)->refresh($user);

        return $user;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
