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

use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as BaseAuthorizationCheckerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
interface AuthorizationCheckerInterface extends BaseAuthorizationCheckerInterface
{
    public function isFeatureEnabled(string $name): bool;

    public function isInTheSameCity($object, $user = null): bool;
}
