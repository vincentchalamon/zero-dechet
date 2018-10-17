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

namespace App\Tests\Constraints;

use App\Constraints\CurrentPassword;
use PHPUnit\Framework\TestCase;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class CurrentPasswordTest extends TestCase
{
    private $constraint;

    protected function setUp()
    {
        $this->constraint = new CurrentPassword();
    }

    public function testMessageShouldBeValid()
    {
        $this->assertEquals('Le mot de passe est invalide.', $this->constraint->getMessage());
    }

    public function testGetTargetsShouldBeValid()
    {
        $this->assertEquals(CurrentPassword::CLASS_CONSTRAINT, $this->constraint->getTargets());
    }
}
