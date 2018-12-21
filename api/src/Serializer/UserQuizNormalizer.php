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

use App\Entity\Choice;
use App\Entity\UserQuiz;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\Serializer\Normalizer\ContextAwareDenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerAwareTrait;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class UserQuizNormalizer implements ContextAwareDenormalizerInterface, DenormalizerAwareInterface
{
    use DenormalizerAwareTrait;

    private $registry;

    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function denormalize($data, $class, $format = null, array $context = [])
    {
        $choices = $data['choices'];
        unset($data['choices']);
        /** @var UserQuiz $userQuiz */
        $userQuiz = $this->denormalizer->denormalize($data, $class, $format, $context);
        foreach ($choices as $id) {
            $choice = $this->registry->getRepository(Choice::class)->find($id);
            if (null !== $choice) {
                $userQuiz->addChoice($choice);
            }
        }

        return $userQuiz;
    }

    public function supportsDenormalization($data, $type, $format = null, array $context = array())
    {
        return UserQuiz::class === $type && isset($data['choices']);
    }
}
