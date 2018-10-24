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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserValidation
{
    public function __invoke(User $data, Request $request, ManagerRegistry $registry): User
    {
        if (!$request->query->has('token') || $data->getToken() !== $request->query->get('token')) {
            throw new HttpException(401);
        }

        if (!$data->isActive()) {
            $data->setActive(true);
            $em = $registry->getManagerForClass(User::class);
            $em->persist($data);
            $em->flush();
        }

        return $data;
    }
}
