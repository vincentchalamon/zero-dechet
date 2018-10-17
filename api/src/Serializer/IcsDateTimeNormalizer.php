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

namespace App\Serializer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class IcsDateTimeNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = []): string
    {
        return $object->format('e:Ymd\THis');
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return IcsEncoder::FORMAT === $format && $data instanceof \DateTimeInterface;
    }
}
