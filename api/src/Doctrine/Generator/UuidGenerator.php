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

namespace App\Doctrine\Generator;

use Doctrine\ORM\EntityManager;
use Ramsey\Uuid\Doctrine\UuidGenerator as BaseUuidGenerator;
use Ramsey\Uuid\Uuid;

/**
 * todo Remove (useless from latest doctrine + ramsey versions).
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UuidGenerator extends BaseUuidGenerator
{
    public function generate(EntityManager $em, $entity)
    {
        /** @var Uuid $uuid */
        $uuid = parent::generate($em, $entity);

        return $uuid->toString();
    }
}
