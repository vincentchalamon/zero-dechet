<?php

declare(strict_types=1);

namespace DatabaseExtension\ServiceContainer;

use DatabaseExtension\EventSubscriber\DatabaseEventSubscriber;
use Behat\Behat\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @author Vincent Chalamon <vincent@les-tilleuls.coop>
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
