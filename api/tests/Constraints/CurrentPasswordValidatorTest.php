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

namespace App\Tests\Constraints;

use App\Constraints\CurrentPassword;
use App\Constraints\CurrentPasswordValidator;
use App\Entity\User;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Violation\ConstraintViolationBuilderInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class CurrentPasswordValidatorTest extends TestCase
{
    private $userPasswordEncoderMock;
    private $userMock;
    private $constraintMock;
    private $contextMock;
    private $validator;

    protected function setUp()
    {
        $this->userPasswordEncoderMock = $this->prophesize(UserPasswordEncoderInterface::class);
        $this->userMock = $this->prophesize(User::class);
        $this->constraintMock = $this->prophesize(CurrentPassword::class);
        $this->contextMock = $this->prophesize(ExecutionContextInterface::class);

        $this->validator = new CurrentPasswordValidator($this->userPasswordEncoderMock->reveal());
        $this->validator->initialize($this->contextMock->reveal());
    }

    public function testThrowExceptionOnInvalidConstraint()
    {
        $this->expectException(\Symfony\Component\Validator\Exception\UnexpectedTypeException::class);

        $this->validator->validate($this->userMock->reveal(), $this->prophesize(Constraint::class)->reveal());
    }

    public function testBuildViolationOnEmptyCurrentPassword()
    {
        $violationMock = $this->prophesize(ConstraintViolationBuilderInterface::class);

        $this->userMock->getPassword()->willReturn('encodedPassword')->shouldBeCalledTimes(1);
        $this->userMock->getPlainPassword()->willReturn('plainPassword')->shouldBeCalledTimes(1);
        $this->userMock->getCurrentPassword()->shouldBeCalledTimes(1);
        $this->userPasswordEncoderMock->isPasswordValid(Argument::any(), Argument::any())->shouldNotBeCalled();

        $this->constraintMock->getMessage()->willReturn('Password is invalid.')->shouldBeCalledTimes(1);
        $this->contextMock->buildViolation('Password is invalid.')->willReturn($violationMock)->shouldBeCalledTimes(1);
        $violationMock->atPath('currentPassword')->willReturn($violationMock)->shouldBeCalledTimes(1);
        $violationMock->addViolation()->shouldBeCalledTimes(1);

        $this->validator->validate($this->userMock->reveal(), $this->constraintMock->reveal());
    }

    public function testBuildViolationOnInvalidCurrentPassword()
    {
        $violationMock = $this->prophesize(ConstraintViolationBuilderInterface::class);

        $this->userMock->getPassword()->willReturn('encodedPassword')->shouldBeCalledTimes(1);
        $this->userMock->getPlainPassword()->willReturn('plainPassword')->shouldBeCalledTimes(1);
        $this->userMock->getCurrentPassword()->willReturn('currentPassword')->shouldBeCalledTimes(2);
        $this->userPasswordEncoderMock->isPasswordValid($this->userMock, 'currentPassword')->willReturn(false)->shouldBeCalledTimes(1);

        $this->constraintMock->getMessage()->willReturn('Password is invalid.')->shouldBeCalledTimes(1);
        $this->contextMock->buildViolation('Password is invalid.')->willReturn($violationMock)->shouldBeCalledTimes(1);
        $violationMock->atPath('currentPassword')->willReturn($violationMock)->shouldBeCalledTimes(1);
        $violationMock->addViolation()->shouldBeCalledTimes(1);

        $this->validator->validate($this->userMock->reveal(), $this->constraintMock->reveal());
    }

    public function testCurrentPasswordIsValid()
    {
        $violationMock = $this->prophesize(ConstraintViolationBuilderInterface::class);

        $this->userMock->getPassword()->willReturn('encodedPassword')->shouldBeCalledTimes(1);
        $this->userMock->getPlainPassword()->willReturn('plainPassword')->shouldBeCalledTimes(1);
        $this->userMock->getCurrentPassword()->willReturn('currentPassword')->shouldBeCalledTimes(2);
        $this->userPasswordEncoderMock->isPasswordValid($this->userMock, 'currentPassword')->willReturn(true)->shouldBeCalledTimes(1);

        $this->constraintMock->getMessage()->shouldNotBeCalled();
        $this->contextMock->buildViolation('Password is invalid.')->shouldNotBeCalled();
        $violationMock->atPath('currentPassword')->shouldNotBeCalled();
        $violationMock->addViolation()->shouldNotBeCalled();

        $this->validator->validate($this->userMock->reveal(), $this->constraintMock->reveal());
    }
}
