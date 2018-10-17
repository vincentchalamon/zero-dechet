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

namespace App\Security;

use ApiPlatform\Core\Security\ExpressionLanguage as BaseExpressionLanguage;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class ExpressionLanguage extends BaseExpressionLanguage
{
    protected function registerFunctions()
    {
        parent::registerFunctions();

        $this->register('is_feature_enabled', function ($feature) {
            return \sprintf('$auth_checker->isFeatureEnabled(%s)', $feature);
        }, function (array $variables, $feature) {
            return $variables['auth_checker']->isFeatureEnabled($feature);
        });

        $this->register('is_in_the_same_city', function ($object) {
            return \sprintf('$auth_checker->isInTheSameCity(%s)', $object);
        }, function (array $variables, $object) {
            return $variables['auth_checker']->isInTheSameCity($object);
        });
    }
}
