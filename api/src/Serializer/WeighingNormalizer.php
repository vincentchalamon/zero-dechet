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

namespace App\Serializer;

use ApiPlatform\Core\JsonLd\Serializer\ItemNormalizer;
use App\Entity\Weighing;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class WeighingNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    private $ready = true;

    /**
     * @param array $weighings
     */
    public function normalize($weighings, $format = null, array $context = []): array
    {
        $this->ready = false;
        $data = $this->normalizer->normalize($weighings, $format, $context);
        $this->ready = true;
        if (!\is_array($weighings)) {
            $weighings = \iterator_to_array($weighings);
        }
        if (!\count($weighings) || !\array_values($weighings)[0] instanceof Weighing) {
            return $data;
        }
        $data += [
            'totalWeight' => \array_sum(\array_map(function (Weighing $weighing) {
                return $weighing->getTotal();
            }, $weighings)),
            'totalRecyclableWeight' => \array_sum(\array_map(function (Weighing $weighing) {
                return $weighing->getTotal();
            }, \array_filter($weighings, function (Weighing $weighing) {
                return Weighing::TYPE_RECYCLABLE === $weighing->getType();
            }))),
            'totalNonRecyclableWeight' => \array_sum(\array_map(function (Weighing $weighing) {
                return $weighing->getTotal();
            }, \array_filter($weighings, function (Weighing $weighing) {
                return Weighing::TYPE_NON_RECYCLABLE === $weighing->getType();
            }))),
            'totalBioWeight' => \array_sum(\array_map(function (Weighing $weighing) {
                return $weighing->getTotal();
            }, \array_filter($weighings, function (Weighing $weighing) {
                return Weighing::TYPE_BIODEGRADABLE === $weighing->getType();
            }))),
        ];
        $data['averageWeight'] = \round($data['totalWeight'] / \count($weighings), 1);

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return ItemNormalizer::FORMAT === $format && (\is_array($data) || $data instanceof \Traversable) && $this->ready;
    }
}
