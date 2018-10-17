<?php

/*
 * This file is part of the Zero-Dechet project.
 *
 * (c) Vincent Chalamon <vincentchalamon@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace App\Action;

use App\Entity\Event;
use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;

/**
 * todo Optimize using ApiSubresource.
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserEvents
{
    /**
     * @Route(name="api_users_events_item", path="/users/{id}/events.{_format}", methods={"GET"}, defaults={
     *     "_api_resource_class"=Event::class,
     *     "_api_item_operation_name"="events",
     *     "_api_subresource_context"={
     *         "property"="events",
     *         "identifiers"={{"id", User::class, true}},
     *         "collection"=true,
     *         "operationId"="api_users_events_get_subresource"
     *     },
     *     "_api_receive"=false,
     *     "_format"=null
     * })
     */
    public function __invoke(User $user): array
    {
        return $user->getEvents();
    }
}
