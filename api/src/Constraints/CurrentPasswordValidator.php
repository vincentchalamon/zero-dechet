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

namespace App\Constraints;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class CurrentPasswordValidator extends ConstraintValidator
{
    private $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    /**
     * @param User                       $user
     * @param Constraint|CurrentPassword $constraint
     */
    public function validate($user, Constraint $constraint)
    {
        if (!$constraint instanceof CurrentPassword) {
            throw new UnexpectedTypeException($constraint, __NAMESPACE__.'\CurrentPassword');
        }

        if (!empty($user->getPassword()) && !empty($user->getPlainPassword()) && (empty($user->getCurrentPassword()) || !$this->userPasswordEncoder->isPasswordValid($user, $user->getCurrentPassword()))) {
            $this->context->buildViolation($constraint->getMessage())
                ->atPath('currentPassword')
                ->addViolation();
        }
    }
}
