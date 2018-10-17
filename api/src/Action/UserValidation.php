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

use App\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserValidation
{
    /**
     * @Route(name="api_users_validate_item", path="/users/{salt}/validate", methods={"GET"}, defaults={
     *     "_api_resource_class"=User::class,
     *     "_api_item_operation_name"="validate",
     *     "_api_receive"=false
     * })
     */
    public function __invoke(User $user, ManagerRegistry $registry): User
    {
        if (!$user->isActive()) {
            $user->setActive(true);
            $em = $registry->getManagerForClass(User::class);
            $em->persist($user);
            $em->flush();
        }

        return $user;
    }
}
