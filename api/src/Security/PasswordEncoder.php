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

namespace App\Security;

use ApiPlatform\Core\Hydra\Serializer\ConstraintViolationListNormalizer;
use App\Entity\User;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
 */
final class PasswordEncoder
{
    private $userPasswordEncoder;
    private $validator;
    private $serializer;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder, ValidatorInterface $validator, SerializerInterface $serializer)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
        $this->validator = $validator;
        $this->serializer = $serializer;
    }

    public function encodePassword(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $violations = $this->validator->validateProperty($user, 'plainPassword');
        if ($violations->count()) {
            throw new BadRequestHttpException(
                $this->serializer->serialize($violations, ConstraintViolationListNormalizer::FORMAT),
                null,
                0,
                ['Content-Type' => 'application/json']
            );
        }
        $user->setPassword($this->userPasswordEncoder->encodePassword($user, $user->getPlainPassword()));
        $user->eraseCredentials();
    }
}
