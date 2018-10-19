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

use App\Entity\Event;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * todo Optimize using ApiSubresource (ability to post on ApiSubresource).
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserLikeEvent
{
    public function __invoke(Event $data, TokenStorageInterface $tokenStorage, ManagerRegistry $registry): Event
    {
        $user = $tokenStorage->getToken()->getUser();
        if (\in_array($user, $data->getLikes(), true)) {
            throw new BadRequestHttpException();
        }
        $data->addLike($user);
        $em = $registry->getManagerForClass(Event::class);
        $em->persist($data);
        $em->flush();

        return $data;
    }
}
