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

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 *
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
class CurrentPassword extends Constraint
{
    public function getMessage(): string
    {
        return 'Le mot de passe est invalide.';
    }

    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
