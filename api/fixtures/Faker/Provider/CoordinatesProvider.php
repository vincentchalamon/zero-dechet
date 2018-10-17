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

namespace App\Faker\Provider;

use Faker\Provider\Address;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class CoordinatesProvider
{
    public static function coordinates()
    {
        return \sprintf('POINT(%s %s)', Address::longitude(50, 50), Address::latitude(3, 3));
    }
}
