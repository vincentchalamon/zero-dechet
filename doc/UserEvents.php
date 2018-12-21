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
use App\Entity\User;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserEvents
{
    /**
     * @return Event[]
     */
    public function __invoke(User $data): array
    {
        return $data->getEvents();
    }
}
