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

namespace App\Doctrine;

use Doctrine\ORM\Mapping\DefaultEntityListenerResolver;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class EntityListenerResolver extends DefaultEntityListenerResolver
{
    private $entityListeners = [];

    public function addEntityListener(string $className, $entityListener)
    {
        $this->entityListeners[$className] = $entityListener;
    }

    public function resolve($className)
    {
        if (isset($this->entityListeners[$className])) {
            return $this->entityListeners[$className];
        }

        return parent::resolve($className);
    }
}
