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

namespace App\DependencyInjection\CompilerPass;

use App\Doctrine\EntityListenerResolver;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DoctrineEntityListenerCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition(EntityListenerResolver::class);
        foreach ($container->findTaggedServiceIds('doctrine.orm.entity_listener') as $serviceId => $attributes) {
            $service = new Reference($serviceId);
            $definition->addMethodCall('addEntityListener', [$container->getDefinition($service)->getClass(), $service]);
        }
    }
}
