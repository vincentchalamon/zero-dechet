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

use App\Entity\User;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserCollectionDenormalizer implements DenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    public function denormalize($data, $class, $format = null, array $context = []): array
    {
        $users = [];
        foreach ($data as $row) {
            $row['active'] = 'true' === $row['active'] || '1' === $row['active'];
            $users[] = $this->denormalizer->denormalize($row, $class, $format, $context);
        }

        return $users;
    }

    public function supportsDenormalization($data, $type, $format = null): bool
    {
        return CsvEncoder::FORMAT === $format && User::class === $type && \is_array($data) && !isset($data['email']);
    }
}
