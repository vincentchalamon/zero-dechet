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

namespace App\Tests\DependencyInjection\CompilerPass;

use App\DependencyInjection\CompilerPass\DoctrineEntityListenerCompilerPass;
use App\Doctrine\EntityListenerResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DoctrineEntityListenerCompilerPassTest extends TestCase
{
    public function testRegistersEntityListenersAsServices()
    {
        $containerMock = $this->prophesize(ContainerBuilder::class);
        $elrDefinitionMock = $this->prophesize(Definition::class);
        $definitionMock = $this->prophesize(Definition::class);

        $containerMock->getDefinition(EntityListenerResolver::class)->willReturn($elrDefinitionMock)->shouldBeCalledTimes(1);
        $containerMock->findTaggedServiceIds('doctrine.orm.entity_listener')->willReturn([
            'foo' => [],
            'bar' => [],
        ])->shouldBeCalledTimes(1);
        $containerMock->getDefinition('foo')->willReturn($definitionMock)->shouldBeCalledTimes(1);
        $containerMock->getDefinition('bar')->willReturn($definitionMock)->shouldBeCalledTimes(1);
        $definitionMock->getClass()->willReturn('Foo', 'Bar')->shouldBeCalledTimes(2);
        $elrDefinitionMock->addMethodCall('addEntityListener', ['Foo', Argument::type(Reference::class)]);
        $elrDefinitionMock->addMethodCall('addEntityListener', ['Bar', Argument::type(Reference::class)]);

        $compilerPass = new DoctrineEntityListenerCompilerPass();
        $compilerPass->process($containerMock->reveal());
    }
}
