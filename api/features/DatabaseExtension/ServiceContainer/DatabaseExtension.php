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

namespace DatabaseExtension\ServiceContainer;

use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use DatabaseExtension\EventSubscriber\DatabaseEventSubscriber;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DatabaseExtension implements ExtensionInterface
{
    public function getConfigKey()
    {
        return 'database';
    }

    public function load(ContainerBuilder $container, array $config)
    {
        $container
            ->register(DatabaseEventSubscriber::class, DatabaseEventSubscriber::class)
            ->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);
    }

    public function process(ContainerBuilder $container)
    {
    }

    public function initialize(ExtensionManager $extensionManager)
    {
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }
}
