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

namespace DatabaseExtension\EventSubscriber;

use Behat\Behat\EventDispatcher\Event\OutlineTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\SuiteTested;
use DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @author Vincent Chalamon <vincentchalamon@gmail.com>
 */
final class DatabaseEventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            SuiteTested::AFTER_SETUP => 'disableAutocommit',
            ScenarioTested::AFTER_SETUP => 'beginTransaction',
            OutlineTested::AFTER_SETUP => 'beginTransaction',
            ScenarioTested::BEFORE_TEARDOWN => 'rollbackTransaction',
            OutlineTested::BEFORE_TEARDOWN => 'rollbackTransaction',
        ];
    }

    public function disableAutocommit()
    {
        StaticDriver::setKeepStaticConnections(true);
    }

    public function beginTransaction()
    {
        StaticDriver::beginTransaction();
    }

    public function rollbackTransaction()
    {
        StaticDriver::rollBack();
    }
}
