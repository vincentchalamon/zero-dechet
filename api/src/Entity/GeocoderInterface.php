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

namespace App\Entity;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
interface GeocoderInterface
{
    public function getFullAddress(): string;

    public function setCoordinates(string $coordinates): void;

    public function getCoordinates(): ?string;

    public function setLongitude(float $longitude): void;

    public function getLongitude(): float;

    public function setLatitude(float $latitude): void;

    public function getLatitude(): float;
}
