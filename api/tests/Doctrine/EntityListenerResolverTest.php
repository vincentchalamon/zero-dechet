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

namespace App\Tests\Doctrine;

use App\Doctrine\EntityListenerResolver;
use PHPUnit\Framework\TestCase;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class EntityListenerResolverTest extends TestCase
{
    /**
     * @var EntityListenerResolver
     */
    private $resolver;

    protected function setUp()
    {
        $this->resolver = new EntityListenerResolver();
    }

    public function testResolvesAnAddedEntityListener()
    {
        $entityListenerMock = $this->prophesize(\stdClass::class)->reveal();

        $this->resolver->addEntityListener(\stdClass::class, $entityListenerMock);
        $this->assertEquals($entityListenerMock, $this->resolver->resolve(\stdClass::class));
    }

    public function testCreatesANewEntityListener()
    {
        $this->assertEquals(new \stdClass(), $this->resolver->resolve(\stdClass::class));
    }
}
